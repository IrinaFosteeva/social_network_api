<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
    protected $fillable = ['status'];

    /**
     * Get the users associated with this account status.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

