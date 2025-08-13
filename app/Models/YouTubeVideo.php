<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class YoutubeVideo extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'youtube_video_id',
        'video_url',
        'title',
        'description',
        'privacy',
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($video) {
            if (empty($video->id)) {
                $video->id = (string) Str::uuid();
            }
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}

