<?php

namespace App\Services\SpotifyApi\Connect;

use App\Models\Setting;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class SpotifyApiConnect
{
    private $session;

    private $api;

    private $configValues; // spotify config file

    private $response;

    private $refreshToken;

    private $command;

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

    public function __construct(?Command $command = null)
    {
        $this->command = $command;
        $this->configValues = config('spotify');

        if (!isset($this->configValues['spotify_client_id'])) {
            Logger::log('error', $this->channel, 'Spotify config missing.', [], $this->command);
            throw new RuntimeException('Spotify config missing: spotify_client_id.');
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
                //$this->response = response()->success('Spotify: Connected with Access token!');
                Logger::log('success', $this->channel, 'Spotify: Connected with Access token!');

                return true;
            }
        } catch (\Exception $e) {

            $this->setupSession();

            if ($refreshToken != '') {

                $this->session->refreshAccessToken($refreshToken);
                $accessToken = $this->session->getAccessToken();

                $this->api->setAccessToken($accessToken);

                if ($this->api->me()) {
                    Logger::log('success', $this->channel, 'Spotify: Connected with Access token!');

                    return;
                }
            }
        }
        $this->api = null;

        Logger::log('error', $this->channel, 'Spotify: No valid connection. Try to re-authorize.', [], $this->command);
        return false;
    }

    public function getAuthorizeUrl()
    {
        $this->setupSession();

        $url = $this->session->getAuthorizeUrl($this->spotifyPermissionOptions);

        return $url;
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
}
