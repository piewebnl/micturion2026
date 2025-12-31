<?php

namespace App\Traits\Converters;

use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifySearchAlbum;
use App\Models\Spotify\SpotifySearchResultAlbum;

trait ToSpotifyAlbumConverter
{
    public function convertSpotifySearchAlbumToSpotifyAlbum(SpotifySearchAlbum $spotifySearchAlbum): array
    {
        return [
            'spotify_api_album_id' => $spotifySearchAlbum['album_id'],
            'name' => $spotifySearchAlbum['name'],
            'artist' => $spotifySearchAlbum['artist'],
            'year' => $spotifySearchAlbum['year'],
            'artwork_url' => $spotifySearchAlbum['artwork_url'],
        ];
    }

    public function convertSpotifySearchResultAlbumToSpotifyAlbum(SpotifySearchResultAlbum $spotifySearchResultAlbum): SpotifyAlbum
    {
        $spotifyAlbum = new SpotifyAlbum;

        return $spotifyAlbum->fill(
            [
                'spotify_api_album_id' => $spotifySearchResultAlbum['spotify_api_album_id'],
                'name' => $spotifySearchResultAlbum['name'],
                'name_sanitized' => $spotifySearchResultAlbum['name_sanitized'],
                'artist' => $spotifySearchResultAlbum['artist'],
                'artwork_url' => $spotifySearchResultAlbum['artwork_url'],
            ]
        );
    }

    public function convertSpotifyAlbumToSongSpotifyAlbum(SpotifyAlbum $spotifyAlbum): array
    {
        return [
            'song_id' => $spotifyAlbum['song_id'],
            'spotify_track_id' => $spotifyAlbum->id,
            'score' => $spotifyAlbum['score'],
            'status' => $spotifyAlbum['status'],
            'artwork_url' => $spotifyAlbum['artwork_url'],
            'search_name' => $spotifyAlbum['search_name'],
            'search_album' => $spotifyAlbum['search_album'],
            'search_artist' => $spotifyAlbum['search_artist'],

        ];
    }
}
