<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ComplaintTicket;
use App\Models\ComplaintMessage;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    public function index()
    {
        $tickets = ComplaintTicket::where('user_id', Auth::id())
            ->with(['messages' => function($q) {
                $q->latest()->limit(1);
            }, 'user'])
            ->orderBy('last_message_at', 'DESC')
            ->get();

        return response()->json($tickets);
    }

    /**
     * Get all tickets for IT/Admin
     */
    public function adminIndex()
    {
        if (!Auth::user()->hasRole('IT')) {
            abort(403);
        }

        $tickets = ComplaintTicket::with(['user', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'DESC')
            ->get();

        return Inertia::render('Complaints/AdminIndex', [
            'tickets' => $tickets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|image|max:5120',
        ]);

        return DB::transaction(function() use ($request) {
            $ticket = ComplaintTicket::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'status' => 'pending',
                'last_message_at' => now(),
            ]);

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('complaints', 'public');
            }

            ComplaintMessage::create([
                'ticket_id' => $ticket->id,
                'sender_id' => Auth::id(),
                'message' => $request->message,
                'attachment_path' => $attachmentPath,
            ]);

            return response()->json([
                'success' => true,
                'ticket' => $ticket->load(['messages', 'user']),
            ]);
        });
    }

    public function show(ComplaintTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id() && !Auth::user()->hasRole('IT')) {
            abort(403);
        }

        return response()->json($ticket->load(['messages.sender', 'user']));
    }

    public function sendMessage(Request $request, ComplaintTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id() && !Auth::user()->hasRole('IT')) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|image|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('complaints', 'public');
        }

        $message = ComplaintMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
        ]);

        // If IT replies, mark as ongoing if it was pending
        if (Auth::user()->hasRole('IT') && $ticket->status === 'pending') {
            $ticket->status = 'ongoing';
        }

        $ticket->last_message_at = now();
        $ticket->save();

        return response()->json($message->load('sender'));
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, ComplaintTicket $ticket)
    {
        if (!Auth::user()->hasRole('IT')) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,ongoing,resolved'
        ]);

        $ticket->update(['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $request->status]);
    }
}
