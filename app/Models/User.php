<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    // Add 'profile_picture' to the $fillable array
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',  // Added role field
        'profile_picture',  // Added profile_picture field
    ];

    // Hide sensitive fields like password and remember_token
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Define the casts for attributes
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Optional: Method to check if the user is an admin
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // Optional: Method to check if the user is a regular user
    public function isUser()
    {
        return $this->role === self::ROLE_USER;
    }
}
