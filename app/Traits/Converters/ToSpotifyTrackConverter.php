<?php

namespace App\Traits\Converters;

use App\Models\Spotify\SpotifySearchResultTrack;
use App\Models\Spotify\SpotifySearchTrack;
use App\Models\Spotify\SpotifyTrack;

trait ToSpotifyTrackConverter
{
    public function convertSpotifyApiPlaylistTrackToSpotifyTrack(object $spotifyApiPlaylistTrack): array
    {
        $artworkUrl = null;
        if (isset($spotifyApiPlaylistTrack->album->images[0]->url)) {
            $artworkUrl = $spotifyApiPlaylistTrack->album->images[0]->url;
        }

        return [
            'spotify_api_track_id' => $spotifyApiPlaylistTrack->id,
            'artist' => $spotifyApiPlaylistTrack->artists[0]->name,
            'name' => $spotifyApiPlaylistTrack->name,
            'album' => $spotifyApiPlaylistTrack->album->name,
            'track_number' => $spotifyApiPlaylistTrack->track_number,
            'disc_number' => $spotifyApiPlaylistTrack->disc_number,
            'spotify_api_album_id' => $spotifyApiPlaylistTrack->album->id,
            'artwork_url' => $artworkUrl,

        ];
    }

    public function convertSpotifySearchTrackToSpotifyTrack(SpotifySearchTrack $spotifySearchTrack): array
    {
        return [
            'spotify_api_track_id' => $spotifySearchTrack['id'],
            'name' => $spotifySearchTrack['name'],
            'album' => $spotifySearchTrack['album'],
            'artist' => $spotifySearchTrack['artist'],
            'year' => $spotifySearchTrack['year'],
            'track_number' => $spotifySearchTrack['track_number'],
            'disc_number' => $spotifySearchTrack['disc_number'],
            'spotify_api_album_id' => $spotifySearchTrack['album_id'],
            'artwork_url' => $spotifySearchTrack['artwork_url'],

        ];
    }

    public function convertSpotifySearchResultTrackToSpotifyTrack(SpotifySearchResultTrack $spotifySearchResultTrack): SpotifyTrack
    {
        $spotifyTrack = new SpotifyTrack;

        return $spotifyTrack->fill(
            [
                'spotify_api_track_id' => $spotifySearchResultTrack['spotify_api_track_id'],
                'spotify_api_album_id' => $spotifySearchResultTrack['spotify_api_album_id'],
                'name' => $spotifySearchResultTrack['name'],
                'album' => $spotifySearchResultTrack['album'],
                'artist' => $spotifySearchResultTrack['artist'],
                'name_sanitized' => $spotifySearchResultTrack['name_sanitized'],
                'album_sanitized' => $spotifySearchResultTrack['album_sanitized'],
                'year' => $spotifySearchResultTrack['year'],
                'track_number' => $spotifySearchResultTrack['track_number'],
                'disc_number' => $spotifySearchResultTrack['disc_number'],
                'artwork_url' => $spotifySearchResultTrack['artwork_url'],
            ]
        );
    }

    /*
    public function convertSpotifyTrackToSongSpotifyTrack(SpotifyTrack $spotifyTrack): array
    {
        return [
            'song_id' => $spotifyTrack['song_id'],
            'spotify_track_id' => $spotifyTrack->id,
            'score' => $spotifyTrack['score'],
            'status' => $spotifyTrack['status'],
            'artwork_url' => $spotifyTrack['artwork_url'],
            'search_name' => $spotifyTrack['search_name'],
            'search_album' => $spotifyTrack['search_album'],
            'search_artist' => $spotifyTrack['search_artist'],

        ];
    }
    */
}
