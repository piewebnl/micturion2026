<?php

namespace App\Scopes;

trait AlbumSpotifyAlbumScopesTrait
{
    public function scopeAlbumSpotifyAlbumWhereKeyword($query, $filterValues)
    {
        // Search trough a lot of fields
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('albums.name', 'like', '%' . $filterValues['keyword'] . '%')->orWhere('artists.name', 'like', '%' . $filterValues['keyword'] . '%');
        }
    }
}
