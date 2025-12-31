<?php

namespace App\Services\SpotifyApi\Connect;

use App\Models\Setting;
use App\Traits\Logger\Logger;
use Illuminate\Http\JsonResponse;

class SpotifyApiConnect
{
    private $session;

    private $api;

    private $configValues; // spotify config file

    private $response;

    private $refreshToken;

    private $channel = 'spotify_api_connect';

    public $hideResponse = false;

    private $spotifyPermissionOptions = [
        'scope' => [
            'playlist-read-private',
            'playlist-read-collaborative',
            'playlist-modify-private',
            'playlist-modify-public',
            'user-read-playback-state',
            'user-read-currently-playing',
            'user-read-recently-played',
            'user-read-private',
            'user-library-read',
            'user-library-modify',
            'user-follow-read',
            'user-follow-modify',
            'ugc-image-upload',
        ],
    ];

    public function __construct()
    {
        $this->configValues = config('spotify');

        if (!isset($this->configValues['spotify_client_id'])) {
            exit('Sorry no Spotify Config found');
        }
    }

    public function setupConnection()
    {
        $this->api = new \SpotifyWebAPI\SpotifyWebAPI;

        $refreshToken = Setting::getSetting('spotify_refresh_token');
        if ($this->refreshToken) {
            $refreshToken = $this->refreshToken;
        }

        try {

            if ($this->api->me()) {
                $this->response = response()->success('Spotify: Connected with Access token!');
                Logger::log('success', $this->channel, 'Spotify: Connected with Access token!');

                return;
            }
        } catch (\Exception $e) {

            $this->setupSession();

            if ($refreshToken != '') {

                $this->session->refreshAccessToken($refreshToken);
                $accessToken = $this->session->getAccessToken();

                $this->api->setAccessToken($accessToken);

                if ($this->api->me()) {
                    $this->response = response()->success('Spotify: Connected with Access token!');
                    Logger::log('success', $this->channel, 'Spotify: Connected with Access token!');

                    return;
                }
            }
        }
        $this->api = null;

        $this->response = response()->error('Spotify: No valid connection. Try to re-authorize.');
        Logger::log('error', $this->channel, 'Spotify: No valid connection. Try to re-authorize.');
    }

    public function getAuthorizeUrl()
    {
        $this->setupSession();

        $url = $this->session->getAuthorizeUrl($this->spotifyPermissionOptions);
        $this->response = response()->json($url, 200);
    }

    public function callback()
    {

        $this->setupSession();

        $this->session->requestAccessToken($_GET['code']);

        $accessToken = $this->session->getAccessToken();
        $refreshToken = $this->session->getRefreshToken();

        Setting::addSetting('spotify_access_token', $accessToken);
        Setting::addSetting('spotify_refresh_token', $refreshToken);

        header('Location: /admin/spotify');
        exit;
    }

    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    private function setupSession()
    {

        $this->session = new \SpotifyWebAPI\Session(
            $this->configValues['spotify_client_id'],
            $this->configValues['spotify_client_secret'],
            $this->configValues['spotify_callback'],
        );
    }

    public function getApi()
    {
        if ($this->api == null) {
            $this->setupConnection();
        }

        return $this->api;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
