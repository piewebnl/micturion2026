<?php

namespace App\Scopes;

trait AlbumScopesTrait
{
    public function scopeAlbumWhereKeyword($query, $filterValues)
    {
        // Search trough a lot of fields
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('artists.name', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orwhere('albums.name', 'LIKE', '%' . $filterValues['keyword'] . '%');
        }
    }

    public function scopeAlbumWithoutDiscogsWhereMatched($query, $filterValues)
    {
        // Search trough a lot of fields
        if (isset($filterValues['matched']) and $filterValues['matched'] != 'all') {
            if ($filterValues['matched'] == 'all') {
                return $query->orWhere('discogs_releases.release_id', '=', '0');
            }
            if ($filterValues['matched'] == 'not_skipped') {
                return $query->orWhere('discogs_releases.release_id', '<>', '0');
            }
            if ($filterValues['matched'] == 'skipped') {
                return $query->orWhere('discogs_releases.release_id', '=', '0');
            }
        }
    }
}
