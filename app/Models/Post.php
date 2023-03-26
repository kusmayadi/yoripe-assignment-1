<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->user_id = Auth::user()->id;
            $post->updated_by = Auth::user()->id;
        });

        static::updating(function (Post $post) {
            $post->updated_by = Auth::user()->id;
        });
    }
}
