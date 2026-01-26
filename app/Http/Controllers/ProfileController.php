<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('role');
        
        // Get activity data for the last 30 days for this user
        $activityData = ActivityLog::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return Inertia::render('Profile/Index', [
            'user' => $user,
            'activityData' => $activityData,
            'profilePhotoUrl' => $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null,
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:1024',
        ]);

        $user = Auth::user();
        
        if ($user->profile_photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile_photos', 'public');
        
        $user->update(['profile_photo_path' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
