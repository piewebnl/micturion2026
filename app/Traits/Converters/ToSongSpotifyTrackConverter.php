<?php

namespace App\Traits\Converters;

use App\Models\SongSpotifyTrack\SongSpotifyTrack;
use App\Models\Spotify\SpotifySearchResultTrack;

trait ToSongSpotifyTrackConverter
{
    public function convertSpotifySearchResultTrackToSongSpotifyTrack(SpotifySearchResultTrack $spotifySearchResultTrack): SongSpotifyTrack
    {
        $spotifyTrack = new SongSpotifyTrack;

        return $spotifyTrack->fill(
            [
                'song_id' => $spotifySearchResultTrack['song_id'],
                'spotify_track_id' => $spotifySearchResultTrack->spotify_track_id,
                'score' => $spotifySearchResultTrack['score'],
                'status' => $spotifySearchResultTrack['status'],
                'artwork_url' => $spotifySearchResultTrack['artwork_url'],
                'search_name' => $spotifySearchResultTrack['search_name'],
                'search_album' => $spotifySearchResultTrack['search_album'],
                'search_artist' => $spotifySearchResultTrack['search_artist'],
            ]
        );
    }
}
