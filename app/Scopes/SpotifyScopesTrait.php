<?php

namespace App\Scopes;

trait SpotifyScopesTrait
{
    public function scopeSpotifyAlbumWhereKeyword($query, $filterValues)
    {
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('artists.name', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orwhere('albums.name', 'LIKE', '%' . $filterValues['keyword'] . '%');
        }
    }

    public function scopeSpotifyTrackWhereKeyword($query, $filterValues)
    {
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('artists.name', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orwhere('albums.name', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orwhere('songs.name', 'LIKE', '%' . $filterValues['keyword'] . '%');
        }
    }
}
