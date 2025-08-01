<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YoutubeToken;
use alchemyguy\YoutubeLaravelApi\AuthenticateService;
use alchemyguy\YoutubeLaravelApi\VideoService;
use App\Models\Video;

class YoutubeVideoController extends Controller
{

    protected function getAuthService(string $identifier)
    {
        $record = YoutubeToken::where('identifier', $identifier)->first();
        if (!$record) {
            throw new \Exception("Token not found for identifier {$identifier}");
        }

        $auth = new AuthenticateService();
        $expiresIn = max(1, now()->diffInSeconds($record->expires_at, false));
        $auth->setAccessToken([
            'access_token' => $record->access_token,
            'refresh_token' => $record->refresh_token,
            'expires_in' => $expiresIn,
        ]);

        return $auth;
    }


    public function upload(Request $request)
    {
        $request->validate([
            'identifier'  => 'required|string',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video'       => 'required|file|mimes:mp4,mov,avi|max:204800',
        ]);

        $auth = $this->getAuthService($request->identifier);
        $token = $auth->getAccessToken();

        if (isset($token['access_token'])) {
            YoutubeToken::updateOrCreate(
                ['identifier' => $request->identifier],
                [
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? null,
                    'expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
                ]
            );
        }

        $vs = new VideoService();
        $data = [
            'title'        => $request->title,
            'description'  => $request->description ?? '',
            'tags'         => $request->tags ?? [],
            'category_id'  => $request->category_id ?? '22',
            'video_status' => $request->video_status ?? 'public',
        ];

        $resp = $vs->uploadVideo($auth->getAccessToken(), $request->file('video')->getRealPath(), $data);


        $video = Video::create([
            'title' => $request->title,
            'youtube_video_id' => $resp['id'] ?? null,
            'youtube_url' => isset($resp['id']) ? "https://www.youtube.com/watch?v={$resp['id']}" : null,
        ]);

        return response()->json(['response' => $resp, 'video' => $video]);
    }

    // جلب تفاصيل فيديو حسب ID
    public function show(Request $request, string $identifier)
    {
        $auth = $this->getAuthService($identifier);
        $vs = new VideoService();
        $resp = $vs->videosListById('snippet,contentDetails,statistics', ['id' => $request->video_id]);
        return response()->json($resp);
    }


    public function delete(Request $request, string $identifier)
    {
        $auth = $this->getAuthService($identifier);
        $vs = new VideoService();
        $resp = $vs->deleteVideo($auth->getAccessToken(), $request->video_id);
        return response()->json($resp);
    }
    public function rate(Request $request, string $identifier)
    {
        $auth = $this->getAuthService($identifier);
        $vs = new VideoService();
        $resp = $vs->videosRate($auth->getAccessToken(), $request->video_id, $request->rating);
        return response()->json($resp);
    }
}
