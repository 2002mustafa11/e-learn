<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'price',
        'payment_status',
        'payment_method',
        'purchased_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
