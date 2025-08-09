<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class YouTubeVideo extends Model
{
    protected $table = 'youtube_videos';
    public $incrementing = false;
    protected $keyType = 'string';
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });
    }
    protected $fillable = [
        'user_id', 'youtube_video_id', 'video_url',
        'title', 'description', 'privacy', 'uploaded_at'
    ];

    protected $dates = ['uploaded_at'];

    public function lesson()
    {
        return $this->morphOne(Lesson::class, 'content');
    }
}


