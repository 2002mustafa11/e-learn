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

}

