<?php

namespace App\Traits\Converters;

use App\Models\Spotify\SpotifySearchResultTrack;
use App\Models\Spotify\SpotifySearchTrack;

trait ToSpotifySearchResultTrackConverter
{
    public function convertSpotifyApiTrackToSpotifySearchResultTrack(object $spotifyApiTrack, array $score, string $status, SpotifySearchTrack $spotifySearchTrack): SpotifySearchResultTrack
    {

        $artworkUrl = null;
        if (isset($spotifyApiTrack->album->images[0]->url)) {
            $artworkUrl = $spotifyApiTrack->album->images[0]->url;
        }

        if (!isset($spotifyApiTrack->name_sanitized)) {
            $spotifyApiTrack->name_sanitized = null;
        }

        if (!isset($spotifyApiTrack->album->name_sanitized)) {
            $spotifyApiTrack->album->name_sanitized = null;
        }

        $spotifySearchResultTrack = new SpotifySearchResultTrack;

        return $spotifySearchResultTrack->fill([
            'spotify_api_track_id' => $spotifyApiTrack->id,
            'name' => $spotifyApiTrack->name,
            'album' => $spotifyApiTrack->album->name,
            'artist' => $spotifyApiTrack->artists[0]->name,
            'name_sanitized' => $spotifyApiTrack->name_sanitized,
            'album_sanitized' => $spotifyApiTrack->album->name_sanitized,
            'year' => substr($spotifyApiTrack->album->release_date, 0, 4),
            'track_number' => $spotifyApiTrack->track_number,
            'disc_number' => $spotifyApiTrack->disc_number,
            'spotify_api_album_id' => $spotifyApiTrack->album->id,
            'score' => $score['total'],
            'artwork_url' => $artworkUrl,
            'status' => $status,
            'search_name' => $spotifySearchTrack['name'],
            'search_album' => $spotifySearchTrack['album'],
            'search_artist' => $spotifySearchTrack['artist'],
            'song_id' => $spotifySearchTrack['song_id'],
        ]);
    }
}
