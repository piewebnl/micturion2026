<?php

namespace App\Services\Spotify\Importers;

use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyAlbumCustomId;
use App\Models\Spotify\SpotifySearchAlbum;
use App\Models\Spotify\SpotifySearchResultAlbum;
use App\Services\SpotifyApi\Getters\SpotifyApiAlbumGetter;
use App\Traits\Converters\ToSpotifyAlbumConverter;
use App\Traits\Converters\ToSpotifyAlbumCustomIdConverter;
use App\Traits\Converters\ToSpotifySearchResultAlbumConverter;
use Illuminate\Http\JsonResponse;

// Import a spotify album by a given spotify album id and a album
class SpotifyAlbumImporter
{
    use ToSpotifyAlbumConverter;
    use ToSpotifyAlbumCustomIdConverter;
    use ToSpotifySearchResultAlbumConverter;

    private $api;

    private // $response;

    private $resource = [];

    private $album;

    private $spotifySearchResultAlbum;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function import(string $spotifyApiAlbumId, Album $album)
    {
        $this->album = $album;

        // Get spotify album via api directly
        $spotifyAlbumsGetter = new SpotifyApiAlbumGetter($this->api);
        $spotifyApiAlbum = $spotifyAlbumsGetter->get($spotifyApiAlbumId);

        if (!$spotifyApiAlbum) {
            return;
        }

        // Some fake search info we need
        $spotifySearchAlbum = new SpotifySearchAlbum;
        $spotifySearchAlbum->fill(
            [
                'search_name' => $album->name,
                'search_artist' => $album->artist,
                'album_id' => $album->id,
            ]
        );
        $score['total'] = 100; // direct hit

        $this->spotifySearchResultAlbum = $this->convertSpotifyApiAlbumToSpotifySearchResultAlbum($spotifyApiAlbum, $score, 'success', $spotifySearchAlbum);

        $spotifyAlbumCustomId = $this->convertSpotifyApiAlbumToSpotifyAlbumCustomId($spotifyApiAlbum, $album);

        $spotifyAlbumCustomIdModel = new SpotifyAlbumCustomId;
        $spotifyAlbumCustomIdModel->store($spotifyAlbumCustomId);

        $this->storeSpotifyAlbumValid();
    }

    private function storeSpotifyAlbumValid()
    {
        $spotifySearchResultAlbum = new SpotifySearchResultAlbum;
        $spotifyAlbum = $spotifySearchResultAlbum->store($this->spotifySearchResultAlbum);

        $this->resource = SpotifyAlbum::with('AlbumSpotifyAlbum.album.artist')->find($spotifyAlbum->id)->toArray();

        if ($this->resource['album_spotify_album']['status'] == 'success') {
            $this->response = response()->success('Spotify album found', $this->resource);

            return;
        }
        if ($this->resource['album_spotify_album']['status'] == 'warning') {
            $this->response = response()->warning('Spotify album found (low scoring)', $this->resource);

            return;
        }
        $this->response = response()->warning('Spotify album found very low scoring', $this->resource);
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
