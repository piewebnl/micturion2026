<?php

namespace App\Services\Spotify\Exporters;

use App\Models\Playlist\Playlist;
use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Logger\Logger;
use App\Services\Spotify\Creators\SpotifyPlaylistCreator;
use Illuminate\Http\JsonResponse;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

// Export playlist image to spotify
class SpotifyPlaylistImageExporter
{
    private $api;

    private $response;

    private $playlist;

    private $resource = [];

    private string $channel;

    public function __construct($api, Playlist $playlist)
    {
        $this->api = $api;
        $this->playlist = $playlist;
        $this->channel = 'spotify_playlists_image_export';
    }

    public function export(string $image)
    {

        $spotifyPlaylist = new SpotifyPlaylist;
        $foundSpotifyPlaylist = $spotifyPlaylist->getSpotifyPlaylistByName($this->playlist->name);

        // Create playlist if not found
        if (!$foundSpotifyPlaylist) {

            $spotifyPlaylistCreator = new SpotifyPlaylistCreator($this->api);
            $spotifyPlaylistCreator->create($this->playlist->name);
            Logger::log('info', $this->channel, 'Spotify Playlist created: ' . $this->playlist->name);

            // Reload it
            $foundSpotifyPlaylist = $spotifyPlaylist->getSpotifyPlaylistByName($this->playlist->name);
        }

        $imageManager = new ImageManager(new Driver);

        $image = $imageManager->read($image)->cover(500, 500)->toJpeg(85);

        // Verlaag JPEG kwaliteit totdat het bestand < 256 KB is
        $quality = 85;
        while (strlen((string) $image) > 256 * 1024 && $quality > 10) {
            $quality -= 5;
            $image = $imageManager->read($image)->toJpeg($quality);
        }

        if (strlen((string) $image) > 256 * 1024) {
            throw new \Exception('❌ Image kon niet genoeg gecomprimeerd worden (<256KB) zonder teveel kwaliteitsverlies.');
        }

        $base64 = base64_encode((string) $image);

        try {
            $this->api->updatePlaylistImage(
                $foundSpotifyPlaylist->spotify_api_playlist_id,
                $base64
            );
        } catch (\SpotifyWebAPI\SpotifyWebAPIException $e) {
            echo "Error: {$e->getMessage()} (HTTP status: {$e->getCode()})";
            dd();
        }

        // dd($this->api);

        $this->resource = [
            'playlist' => $this->playlist,
            'spotify_playlist' => $foundSpotifyPlaylist,
        ];

        // Logger::log('info', $this->channel, 'Spotify Playlist Tracks exported: ' . $foundSpotifyPlaylist['name'] . ' [' . $this->totalSpotifyPlaylistTracks . ' tracks]');

        // $this->response = response()->success('Spotify Playlist Tracks exported: ' . $foundSpotifyPlaylist['name'], $this->resource);
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
