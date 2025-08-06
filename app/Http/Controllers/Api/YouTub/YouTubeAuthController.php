<?php

namespace App\Http\Controllers\Api\YouTub;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Google\Client;
use App\Models\YoutubeToken;

class YouTubeAuthController extends Controller
{
    private function makeClient(): Client
    {
        $cfg = config('services.youtube_api');

        $client = new Client();
        $client->setAuthConfig(storage_path('app/private/client_secret_820786433841-igvm5ps7q2rgoa762qgonf6eqbpnf6el.apps.googleusercontent.com.json'));
        $client->addScope($cfg['scopes']);
        $client->setRedirectUri($cfg['redirect_uri']);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setIncludeGrantedScopes(true);
        return $client;
    }

    public function getAuthUrl()
    {
        $client = $this->makeClient();
        // $state = bin2hex(random_bytes(16));
        // session(['yt_oauth_state' => $state]);
        // $client->setState($state);

        return response()->json(['auth_url' => $client->createAuthUrl()]);
    }

    public function callback(Request $req)
    {
        // if ($req->error || session('yt_oauth_state') !== $req->state) {
        //     return response()->json(['error' => $req->error ?? 'State mismatch'], 403);
        // }

        $client = $this->makeClient();
        $token = $client->fetchAccessTokenWithAuthCode($req->code);

        if (isset($token['error'])) {
            return response()->json(['error' => $token], 400);
        }

        $client->setAccessToken($token);

        $auth = Auth::user();
        YoutubeToken::updateOrCreate(
            ['user_id' => $auth->id],
            [
                'access_token'  => $token['access_token'],
                'refresh_token' => $client->getRefreshToken(),
                'scope'         => $token['scope'] ?? null,
                'expires_at'    => Carbon::now()->addSeconds($token['expires_in']),
                // 'state'         => $req->state,
            ]
        );

        return response()->json(['status' => 'YouTube connected']);
    }
}