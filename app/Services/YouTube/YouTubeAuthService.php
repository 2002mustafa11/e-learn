<?php

namespace App\Services\YouTube;

use Google\Client;
use Carbon\Carbon;
use App\Repositories\YouTube\YouTubeTokenRepository;

class YouTubeAuthService
{
    private $tokenRepository;

    public function __construct(YouTubeTokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

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
        return $this->makeClient()->createAuthUrl();
    }

    public function handleCallback(string $code)
    {
        $client = $this->makeClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception($token['error_description'] ?? 'Authentication failed');
        }

        $this->tokenRepository->updateOrCreateToken([
            'user_id' => auth()->id(),
            'access_token' => $token['access_token'],
            'refresh_token' => $client->getRefreshToken(),
            'scope' => $token['scope'] ?? null,
            'expires_at' => Carbon::now()->addSeconds($token['expires_in'])
        ]);

        return true;
    }
}