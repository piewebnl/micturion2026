<?php

namespace App\Scopes;

trait SpotifyPlaylistScopesTrait
{
    public function scopeSpotifyPlaylistSortAndOrderBy($query, $filterValues)
    {

        if (!isset($filterValues['sort'])) {
            return;
        }

        if ($filterValues['sort'] == 'name') {

            // default artist
            return $query->orderBy('spotify_playlists.name', $filterValues['order'])
                ->orderBy('spotify_playlist_tracks.order');
        }
    }

    public function scopeSpotifyPlaylistWhereKeyword($query, $filterValues)
    {

        // Search trough a lot of fields
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('spotify_tracks.artist', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('spotify_tracks.album', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('spotify_tracks.name', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('spotify_playlists.name', 'like', '%' . $filterValues['keyword'] . '%');
        }
    }

    public function scopeSpotifyPlaylistWhereName($query, $filterValues)
    {
        if (isset($filterValues['name']) and $filterValues['name'] != '') {
            return $query->where('spotify_playlists.name', 'like', '%' . $filterValues['name'] . '%');
        }
    }

    public function scopeSpotifyPlaylistWhereYear($query, $filterValues)
    {
        if (isset($filterValues['year']) and $filterValues['year'] != '') {
            return $query->where('spotify_playlists.date', 'like', $filterValues['year'] . '%');
        }
    }
}
