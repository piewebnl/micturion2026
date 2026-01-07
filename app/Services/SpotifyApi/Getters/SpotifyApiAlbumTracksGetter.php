<?php

namespace App\Services\SpotifyApi\Getters;

// Get tracks for a spotify album via api
class SpotifyApiAlbumTracksGetter
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function getAll(string $spotifyApiAlbumId, int $limit = 50, string $market = 'NL'): array
    {
        $tracks = [];
        $offset = 0;

        do {
            $response = $this->api->getAlbumTracks($spotifyApiAlbumId, [
                'limit' => $limit,
                'offset' => $offset,
                'market' => $market,
            ]);

            if (!isset($response->items)) {
                break;
            }

            $tracks = array_merge($tracks, $response->items);
            $offset += $limit;
        } while (!empty($response->next));

        return $tracks;
    }
}
