<?php
// app/Services/YouTube/YouTubeUploadService.php
namespace App\Services\YouTube;

use Google\Client;
use Google\Service\YouTube;
use Google\Http\MediaFileUpload;
use Carbon\Carbon;
use App\Repositories\YouTube\YouTubeTokenRepository;
use App\Repositories\YouTube\YouTubeVideoRepository;

class YouTubeUploadService
{
    private $tokenRepository;
    private $videoRepository;

    public function __construct(
        YouTubeTokenRepository $tokenRepository,
        YouTubeVideoRepository $videoRepository
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->videoRepository = $videoRepository;
    }

    private function makeClient(): Client
    {
        $cfg = config('services.youtube_api');
        $client = new Client();
        $client->setAuthConfig(storage_path('app/private/client_secret.json'));
        $client->addScope($cfg['scopes']);
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);

        $yt = $this->tokenRepository->getTokenByUserId(auth()->id());
        $expiresAt = Carbon::parse($yt->expires_at);

        $client->setAccessToken([
            'access_token' => $yt->access_token,
            'refresh_token' => $yt->refresh_token,
            'created' => $expiresAt->copy()->subSeconds(3600)->getTimestamp(),
            'expires_in' => $expiresAt->diffInSeconds(Carbon::now()),
        ]);

        if ($client->isAccessTokenExpired() && $yt->refresh_token) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($yt->refresh_token);
            $this->tokenRepository->updateOrCreateToken([
                'user_id' => auth()->id(),
                'access_token' => $newToken['access_token'],
                'expires_at' => Carbon::now()->addSeconds($newToken['expires_in'])
            ]);
            $client->setAccessToken($newToken);
        }

        return $client;
    }

    public function uploadVideo(array $data, $videoFile)
    {
        try {
            if (!$videoFile->isValid()) {
                throw new \Exception('Invalid video file');
            }

            $client = $this->makeClient();
            $youtube = new YouTube($client);

            $snippet = new YouTube\VideoSnippet();
            $snippet->setTitle(substr($data['title'], 0, 100));
            $snippet->setDescription(substr($data['description'] ?? '', 0, 5000));
            $snippet->setCategoryId('22');

            $status = new YouTube\VideoStatus();
            $status->privacyStatus = $data['privacy'] ?? 'private';

            $video = new YouTube\Video();
            $video->setSnippet($snippet);
            $video->setStatus($status);

            $client->setDefer(true);
            $insertRequest = $youtube->videos->insert('snippet,status', $video);

            $media = new MediaFileUpload(
                $client,
                $insertRequest,
                $videoFile->getMimeType(),
                null,
                true,
                2 * 1024 * 1024
            );

            $media->setFileSize($videoFile->getSize());

            $handle = fopen($videoFile->getRealPath(), 'rb');
            $uploadedVideo = null;
            $chunkSize = 2 * 1024 * 1024;

            while (!$uploadedVideo && !feof($handle)) {
                $chunk = fread($handle, $chunkSize);
                $uploadedVideo = $media->nextChunk($chunk);
            }

            fclose($handle);
            $client->setDefer(false);

            if (empty($uploadedVideo['id'])) {
                throw new \Exception('YouTube upload failed: No video ID returned');
            }

            return $this->videoRepository->createVideo([
                'user_id' => auth()->id(),
                'youtube_video_id' => $uploadedVideo['id'],
                'video_url' => "https://www.youtube.com/watch?v={$uploadedVideo['id']}",
                'title' => $uploadedVideo['snippet']['title'] ?? $data['title'],
                'description' => $uploadedVideo['snippet']['description'] ?? $data['description'] ?? '',
                'privacy' => $uploadedVideo['status']['privacyStatus'] ?? $data['privacy'],
                'thumbnail_url' => $uploadedVideo['snippet']['thumbnails']['default']['url'] ?? null,
            ]);

        } catch (\Google\Service\Exception $e) {
            $error = json_decode($e->getMessage(), true);
            throw new \Exception($error['error']['message'] ?? 'YouTube API error');
        } catch (\Exception $e) {
            throw new \Exception('Video upload failed: ' . $e->getMessage());
        }
    }
}