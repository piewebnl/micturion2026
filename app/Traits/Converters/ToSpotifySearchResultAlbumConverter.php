<?php

namespace App\Traits\Converters;

use App\Models\Spotify\SpotifySearchAlbum;
use App\Models\Spotify\SpotifySearchResultAlbum;

trait ToSpotifySearchResultAlbumConverter
{
    public function convertSpotifyApiAlbumToSpotifySearchResultAlbum(object $spotifyApiAlbum, array $score, string $status, SpotifySearchAlbum $spotifySearchAlbum): SpotifySearchResultAlbum
    {

        $artworkUrl = null;
        if (isset($spotifyApiAlbum->images[0]->url)) {
            $artworkUrl = $spotifyApiAlbum->images[0]->url;
        }

        if (!isset($spotifyApiAlbum->name_sanitized)) {
            $spotifyApiAlbum->name_sanitized = null;
        }

        $spotifySearchResultAlbum = new SpotifySearchResultAlbum;

        return $spotifySearchResultAlbum->fill([
            'spotify_api_album_id' => $spotifyApiAlbum->id,
            'name' => $spotifyApiAlbum->name,
            'name_sanitized' => $spotifyApiAlbum->name_sanitized,
            'artist' => $spotifyApiAlbum->artists[0]->name,
            'year' => substr($spotifyApiAlbum->release_date, 0, 4),
            'score' => $score['total'],
            'artwork_url' => $artworkUrl,
            'status' => $status,
            'search_name' => $spotifySearchAlbum['name'],
            'search_artist' => $spotifySearchAlbum['artist'],
            'album_id' => $spotifySearchAlbum['album_id'],
        ]);
    }
}
