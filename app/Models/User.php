<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_EMPLOYEE = 'employee';
    public const ROLE_SUPERVISOR = 'supervisor';
    public const ROLE_ADMIN = 'admin';


    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function workEntries()
    {
        return $this->hasMany(WorkEntry::class, 'user_id');
    }


    public function enteredWorkEntries()
    {
        return $this->hasMany(WorkEntry::class, 'entered_by_user_id');
    }


    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }
}