<?php
// app/Repositories/YouTube/YouTubeTokenRepository.php
namespace App\Repositories\YouTube;

use App\Models\YoutubeToken;

class YouTubeTokenRepository
{
    public function updateOrCreateToken(array $data)
    {
        return YoutubeToken::updateOrCreate(
            ['user_id' => $data['user_id']],
            $data
        );
    }

    public function getTokenByUserId($userId)
    {
        return YoutubeToken::where('user_id', $userId)->firstOrFail();
    }
}