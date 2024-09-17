<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['type'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

