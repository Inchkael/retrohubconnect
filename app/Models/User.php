<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'password',
        'role',
        'language',
        'avatar',
        'zip',
        'state',
        'address',
        'address2',
        'country',
        'city',
        'birth_date',
        'enabled',
        'last_login',
        'login_attempts',
        'locked_until',
        'locked_by',
        'lock_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'locked_until' => 'datetime',
        'birth_date' => 'date',
        'enabled' => 'boolean',
    ];

    protected $attributes = [
        'role' => 'visitor',
        'language' => 'fr',
        'enabled' => true,
    ];

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
