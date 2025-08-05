<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeToken extends Model
{
    protected $table = 'youtube_tokens';

    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'scope',
        'expires_at',
        'state',
    ];

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
