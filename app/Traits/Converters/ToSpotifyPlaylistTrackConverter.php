<?php

namespace App\Traits\Converters;

use App\Models\Spotify\SpotifyPlaylist;
use App\Models\Spotify\SpotifyTrack;

trait ToSpotifyPlaylistTrackConverter
{
    public function convertSpotifyApiPlaylistToSpotifyPlaylistTrack(object $spotifyApiPlaylistTrack, SpotifyTrack $spotifyTrack, SpotifyPlaylist $spotifyPlaylist, $order)
    {
        return [
            'spotify_track_id' => $spotifyApiPlaylistTrack->id,
            'spotify_track_id' => $spotifyTrack->id,
            'spotify_playlist_id' => $spotifyPlaylist->id,
            'has_changed' => true,
            'order' => $order,
        ];
    }
}
