<?php

namespace App\Scopes;

trait SongSpotifyTrackScopesTrait
{
    public function scopeSongSpotifyTrackWhereKeyword($query, $filterValues)
    {

        // Search trough a lot of fields
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('song_spotify_track.search_artist', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('song_spotify_track.search_album', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('song_spotify_track.search_name', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('songs.name', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('albums.name', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('artists.name', 'like', '%' . $filterValues['keyword'] . '%');
        }
    }
}
