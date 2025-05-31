<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entered_by_user_id',
        'date_of_work',
        'hours_worked',
    ];

    protected $casts = [
        'date_of_work' => 'date',
    ];


    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by_user_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}