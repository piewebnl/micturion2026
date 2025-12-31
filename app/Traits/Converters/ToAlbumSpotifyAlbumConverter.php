<?php

namespace App\Traits\Converters;

use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;
use App\Models\Spotify\SpotifySearchResultAlbum;

trait ToAlbumSpotifyAlbumConverter
{
    public function convertSpotifySearchResultAlbumToAlbumSpotifyAlbum(SpotifySearchResultAlbum $spotifySearchResultAlbum): AlbumSpotifyAlbum
    {
        $spotifyAlbum = new AlbumSpotifyAlbum;

        return $spotifyAlbum->fill(
            [
                'album_id' => $spotifySearchResultAlbum['album_id'],
                'spotify_album_id' => $spotifySearchResultAlbum->spotify_album_id,
                'score' => $spotifySearchResultAlbum['score'],
                'status' => $spotifySearchResultAlbum['status'],
                'artwork_url' => $spotifySearchResultAlbum['artwork_url'],
                'search_name' => $spotifySearchResultAlbum['search_name'],
                'search_artist' => $spotifySearchResultAlbum['search_artist'],
            ]
        );
    }
}
