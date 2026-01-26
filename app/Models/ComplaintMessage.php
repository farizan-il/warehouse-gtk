<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'sender_id',
        'message',
        'attachment_path',
    ];

    public function ticket()
    {
        return $this->belongsTo(ComplaintTicket::class, 'ticket_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
