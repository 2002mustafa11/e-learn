<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'user_id','title', 'duration', 'enrolled', 'lectures',
        'skill_level', 'language', 'fee',
        'description', 'learning_skill'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'course_category');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
