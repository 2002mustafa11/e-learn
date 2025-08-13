<?php

namespace App\Repositories\YouTube;

use App\Models\YouTubeVideo;

class YouTubeVideoRepository
{
    public function createVideo(array $data)
    {
        return YouTubeVideo::create($data);
    }
}