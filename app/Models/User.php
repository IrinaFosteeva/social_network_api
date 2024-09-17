<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'account_status_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sentMessages() {
        return $this->hasMany(Message::class, 'user_id');
    }

    public function isActive() {
        return $this->account_status_id === AccountStatus::where('status', 'active')->value('id');
    }

    public function accountStatus() {
        return $this->belongsTo(AccountStatus::class);
    }

    public function followings(): BelongsToMany {
        return $this->belongsToMany(User::class, 'followings', 'user_id', 'following_id');
    }

    public function followers(): BelongsToMany {
        return $this->belongsToMany(User::class, 'followings', 'following_id', 'user_id');
    }

    public function profile() {
        return $this->hasOne(Profile::class);
    }

}





