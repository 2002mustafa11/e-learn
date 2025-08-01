<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use alchemyguy\YoutubeLaravelApi\AuthenticateService;

class YoutubeAuthController extends Controller
{
    public function login()
    {
        $auth = new AuthenticateService();
        $url = $auth->getLoginUrl('user@example.com', 'unique_identifier');
        return redirect($url);
    }

    public function callback(Request $request)
    {
        $code       = $request->get('code');
        $identifier = $request->get('state');

        $auth = new AuthenticateService();
        $authResponse = $auth->authChannelWithCode($code);

        DB::table('youtube_tokens')->updateOrInsert(
            ['identifier' => $identifier],
            [
                'access_token'          => $authResponse['token']['access_token'],
                'refresh_token'         => $authResponse['token']['refresh_token'] ?? null,
                'expires_at'            => now()->addSeconds($authResponse['token']['expires_in']),
                'channel_details'       => json_encode($authResponse['channel_details']),
                'live_streaming_status' => $authResponse['live_streaming_status'] ?? null,
                'updated_at'            => now(),
            ]
        );

        return redirect('/')->with('success', 'تم الربط بنجاح');
    }
}
