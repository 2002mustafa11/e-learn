<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration',
        'order',
        'is_free',
        'is_published',
        'contentable_id',
        'contentable_type'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function content()
    {
        return $this->morphTo(__FUNCTION__, 'content_type', 'content_id');
    }
}