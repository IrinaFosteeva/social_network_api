<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    use HasFactory;
    protected $table = 'followings';

    public $timestamps = true;


    protected $fillable = [
        'user_id',
        'following_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
