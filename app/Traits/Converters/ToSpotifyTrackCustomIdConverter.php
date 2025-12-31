<?php

namespace App\Traits\Converters;

use App\Models\Music\Song;
use App\Models\Spotify\SpotifyTrackCustomId;

trait ToSpotifyTrackCustomIdConverter
{
    public function convertSpotifyApiTrackToSpotifyTrackCustomId($spotifyApiTrack, Song $song)
    {
        $spotifyTrackCustomId = new SpotifyTrackCustomId;

        return $spotifyTrackCustomId->fill([
            'persistent_id' => $song->persistent_id,
            'spotify_api_track_custom_id' => $spotifyApiTrack->id,
            'album' => $spotifyApiTrack->album->name,
            'artist' => $spotifyApiTrack->artists[0]->name,
            'disk_number' => $spotifyApiTrack->disc_number,
            'track_number' => $spotifyApiTrack->track_number,
            'name' => $spotifyApiTrack->name,
        ]);
    }

    public function convertSongToSpotifyTrackCustomId($songWithSpotifyTrack)
    {
        $spotifyTrackCustomId = new SpotifyTrackCustomId;

        return $spotifyTrackCustomId->fill([
            'persistent_id' => $songWithSpotifyTrack['persistent_id'],
            'spotify_api_track_custom_id' => $songWithSpotifyTrack['spotify_api_track_id'],
            'album' => $songWithSpotifyTrack['album_name'],
            'artist' => $songWithSpotifyTrack['artist_name'],
            'name' => $songWithSpotifyTrack['name'],
        ]);
    }
}
