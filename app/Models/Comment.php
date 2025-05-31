<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_entry_id',
        'user_id',
        'comment_text',
    ];


    public function workEntry()
    {
        return $this->belongsTo(WorkEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}