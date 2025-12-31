<?php

namespace App\Models\Spotify;

use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;
use App\Traits\Converters\ToAlbumSpotifyAlbumConverter;
use App\Traits\Converters\ToSpotifyAlbumConverter;
use Illuminate\Database\Eloquent\Model;

// Pseudo model for best match after spotify album search (spotify album + status)
class SpotifySearchResultAlbum extends Model
{
    use ToAlbumSpotifyAlbumConverter;
    use ToSpotifyAlbumConverter;

    protected $guarded = [];

    private // $response;

    private $resource = [];

    public function store(SpotifySearchResultAlbum $spotifySearchResultAlbum)
    {

        // Store the album
        $spotifyAlbumModel = new SpotifyAlbum;
        $spotifyAlbum = $this->convertSpotifySearchResultAlbumToSpotifyAlbum($spotifySearchResultAlbum);
        // $response = $spotifyAlbumModel->store($spotifyAlbum);

        // Store relation
        $albumSpotifyAlbumModel = new AlbumSpotifyAlbum;
        $spotifySearchResultAlbum->spotify_album_id = // $response->id;

        $albumSpotifyAlbum = $this->convertSpotifySearchResultAlbumToAlbumSpotifyAlbum($spotifySearchResultAlbum);
        $albumSpotifyAlbumModel->store($albumSpotifyAlbum);

        return // $response;
    }
}
