<?php

namespace App\Services\SpotifyApi\Deleters;

use Illuminate\Http\JsonResponse;

// Empty spotify playlist
class SpotifyApiPlaylistEmptier
{
    private $api;

    private $response;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function empty(string $spotifyApiPlaylistId)
    {

        // Dummy song
        $this->api->replacePlaylistTracks($spotifyApiPlaylistId, ['4wYq5wugZDzQiMZQYG4wVB']);
        $fromSpotifyPlaylist = $this->api->getPlaylist($spotifyApiPlaylistId);

        // Remove it
        $this->api->deletePlaylistTracks(
            $spotifyApiPlaylistId,
            [
                'tracks' => [
                    ['uri' => '4wYq5wugZDzQiMZQYG4wVB'],
                ],
            ]
        );

        $this->response = response()->success('Spotify playlist emptied');
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
