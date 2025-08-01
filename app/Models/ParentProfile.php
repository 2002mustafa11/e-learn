<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    protected $fillable = [
        'user_id', 'relation_type', 'job'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }
}

