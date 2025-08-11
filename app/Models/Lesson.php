<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration',
        'order',
        'is_free',
        'is_published',
        'content_type',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function pdfFiles()
    {
        return $this->hasMany(PdfFile::class);
    }

    public function youtubeVideos()
    {
        return $this->hasMany(YoutubeVideo::class);
    }
}
