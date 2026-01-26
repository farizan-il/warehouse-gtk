<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintTicket extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ComplaintMessage::class, 'ticket_id');
    }
}
