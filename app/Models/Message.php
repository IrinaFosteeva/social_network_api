<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['chat_id', 'user_id', 'content', 'status'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statuses()
    {
        return $this->hasMany(MessageStatus::class);
    }
}

