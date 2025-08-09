<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfFile extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'file_path',
        'file_name',
        'file_size',
        'page_count'
    ];

    public function lesson()
    {
        return $this->morphOne(Lesson::class, 'content');
    }
}
