<?php

namespace App\Http\Controllers\Api\YouTub;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Google\Client;
use Google\Service\YouTube;
use Google\Http\MediaFileUpload;
use App\Models\YoutubeToken;
use App\Models\YouTubeVideo;

class YouTubeUploadController extends Controller
{
    private function makeClient(): Client
    {

        $cfg = config('services.youtube_api');
        $client = new Client();
        $client->setAuthConfig(storage_path('app/client_secret_820786433841-igvm5ps7q2rgoa762qgonf6eqbpnf6el.apps.googleusercontent.com.json'));
        // $client->setAuthConfig(base_path('credentials/client_secret.json'));
        $client->addScope($cfg['scopes']);
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $yt = YoutubeToken::where('user_id', Auth::id())->firstOrFail();
        $expiresAt = Carbon::parse($yt->expires_at);
        $client->setAccessToken([
            'access_token'  => $yt->access_token,
            'refresh_token' => $yt->refresh_token,
            'created'       => $expiresAt->copy()->subSeconds(3600)->getTimestamp(),
            'expires_in'    => $expiresAt->diffInSeconds(Carbon::now()),
        ]);

        if ($client->isAccessTokenExpired() && $yt->refresh_token) {
            $new = $client->fetchAccessTokenWithRefreshToken($yt->refresh_token);
            $yt->update([
                'access_token' => $new['access_token'],
                'expires_at'   => Carbon::now()->addSeconds($new['expires_in']),
            ]);
            $client->setAccessToken($new);
        }

        return $client;
    }

    public function upload(Request $r)
    {

        $r->validate([
            'video'       => 'required|file|mimetypes:video/*',
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'privacy'     => 'in:public,unlisted,private',
        ]);
        // dd(Auth::id());
// dd($r->input());
        $client = $this->makeClient();
        $youtube = new YouTube($client);

        $snippet = new YouTube\VideoSnippet();
        $snippet->setTitle(substr($r->title, 0, 200));
        $snippet->setDescription(substr($r->description ?? '', 0, 500));
        $snippet->setCategoryId('22');

        $status = new YouTube\VideoStatus();
        $status->privacyStatus = $r->privacy;

        $video = new YouTube\Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        $client->setDefer(true);
        $insert = $youtube->videos->insert('snippet,status', $video);

        $media = new MediaFileUpload(
            $client,
            $insert,
            $r->file('video')->getMimeType(),
            null,
            true,
            1 * 1024 * 1024
        );
        $media->setFileSize(filesize($r->file('video')->getRealPath()));

        $uploaded = false;
        $handle = fopen($r->file('video')->getRealPath(), 'rb');
        while (!$uploaded && !feof($handle)) {
            $uploaded = $media->nextChunk(fread($handle, 1 * 1024 * 1024));
        }
        fclose($handle);
        $client->setDefer(false);

        if (empty($uploaded['id'])) {
            return response()->json(['error' => 'Upload failed'], 500);
        }

        $videoId = $uploaded['id'];
        $videoUrl = "https://www.youtube.com/watch?v={$videoId}";

        YouTubeVideo::create([
            'user_id'          => Auth::id(),
            'youtube_video_id' => $videoId,
            'video_url'        => $videoUrl,
            'title'            => $uploaded['snippet']['title'] ?? $r->title,
            'description'      => $uploaded['snippet']['description'] ?? '',
            'privacy'          => $r->privacy,
            'uploaded_at'      => Carbon::now(),
        ]);

        return response()->json([
            'videoId'  => $videoId,
            'title'    => $uploaded['snippet']['title'],
            'videoUrl' => $videoUrl,
        ], 201);
    }
}