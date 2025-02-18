<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Import Authenticatable
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Fillable attributes for mass assignment
    protected $fillable = ['name', 'email', 'password'];

    // Optionally, hide password from the model's array or JSON representation
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Optionally, cast attributes to a specific data type
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Ensure password is always hashed when setting
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
}
