<?php

namespace App\Scopes;

trait GlobalScopesTrait
{
    public function scopeWhereKeyword($query, $filterValues)
    {
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('name', 'LIKE', '%' . $filterValues['keyword'] . '%');
        }
    }

    public function scopeWhereName($query, $filterValues)
    {
        if (isset($filterValues['name']) and $filterValues['name'] != '') {
            return $query->where('name', 'LIKE', '%' . $filterValues['name'] . '%');
        }
    }

    public function scopeWhereId($query, array $filterValues, string $field, string $filterValueKey)
    {

        if (isset($filterValues[$filterValueKey])) {

            if (is_array($filterValues[$filterValueKey]) and count($filterValues[$filterValueKey]) > 0) {

                return $query->whereIn($field, $filterValues[$filterValueKey]);
            }

            if (is_array($filterValues[$filterValueKey]) and count($filterValues[$filterValueKey]) == 0) {
                return;
            }

            if ($filterValues[$filterValueKey] != '') {
                return $query->where($field, $filterValues[$filterValueKey]);
            }
        }
    }

    public function scopeSortAndOrderBy($query, $filterValues)
    {

        if (!isset($filterValues['sort'])) {
            return;
        }

        if ($filterValues['sort'] == 'random') {
            return $query->inRandomOrder();
        }

        return $query->orderBy($filterValues['sort'], $filterValues['order']);
    }

    public function scopeCustomPaginateOrLimit($query, $filterValues)
    {

        if (isset($filterValues['limit']) and $filterValues['limit'] > 0) {
            return $query->take($filterValues['limit'])->get();
        }

        if (isset($filterValues['page']) and $filterValues['page']) {
            if (!isset($filterValues['per_page'])) {
                $filterValues['per_page'] = 50;
            }

            return $query->paginate($filterValues['per_page'], '*', 'page', $filterValues['page']);
        }

        return $query->get(); // Always paginate!
    }

    // Wishlist
    public function scopeWishlistAlbumSortAndOrderBy($query, $filterValues)
    {
        return $query->orderBy($filterValues['sort'], $filterValues['order'])->orderBy('album_sort_name')->orderBy('price');
    }

    public function scopeWishlistAlbumWhereKeyword($query, $filterValues)
    {
        // Search trough a lot of fields
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('artists.name', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orwhere('albums.name', 'LIKE', '%' . $filterValues['keyword'] . '%');
        }
    }

    public function scopeWishlistAlbumShowLowScores($query, $filterValues)
    {

        if (isset($filterValues['show_low_scores']) and $filterValues['show_low_scores'] == false) {
            return $query->where('score', '>', 73)->orWhereNull('score');
        }
    }

    public function scopeWishlistAlbumWhereFormat($query, $filterValues)
    {

        if (isset($filterValues['format']) and $filterValues['format'] != '') {
            if ($filterValues['format'] == 'cd') {
                return $query->where('wishlist_album_prices.format', 'cd');
            }
            if ($filterValues['format'] == 'lp') {
                return $query->where('wishlist_album_prices.format', 'LP');
            }
        }
    }

    public function scopeConcertWhereKeyword($query, $filterValues)
    {

        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            $keyword = $filterValues['keyword'];

            return $query->whereHas('concertItems.concertArtist', function ($query) use ($keyword) {
                $query->where('concert_artists.name', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('concertVenue', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            });
        }
    }

    public function scopeConcertWhereName($query, array $filterValues)
    {

        if (isset($filterValues['name']) and $filterValues['name'] != '') {
            $name = $filterValues['name'];

            return $query->whereHas('concertItems.concertArtist', function ($query) use ($name) {
                $query->where('concert_artists.id', $name);
            });
        }
    }

    public function scopeConcertWhereYear($query, $filterValues)
    {
        if (isset($filterValues['year']) and $filterValues['year'] != '') {
            return $query->where('concerts.date', 'like', $filterValues['year'] . '%');
        }
    }

    public function scopeWhereGroupBy($query, array $filterValues)
    {

        if (isset($filterValues['group_by']) and is_array($filterValues['group_by'])) {
            foreach ($filterValues['group_by'] as $groupBy) {
                $query->groupBy($groupBy);
            }
        }

        return $query;
    }

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

    public function scopeDiscogsReleaseWhereKeyword($query, $filterValues)
    {

        // Search trough a lot of fields
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('artists.name', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orwhere('albums.name', 'LIKE', '%' . $filterValues['keyword'] . '%');
        }
    }

    public function scopeDiscogsReleaseWhereMatched($query, $filterValues)
    {

        // Search trough a lot of fields
        if (isset($filterValues['matched']) and $filterValues['matched'] != 'all') {
            if ($filterValues['matched'] == 'matched') {
                return $query->where('score', '>', 50);
            }
            if ($filterValues['matched'] == 'not_matched') {
                return $query->where('score', null);
            }
            if ($filterValues['matched'] == 'skipped') {
                return $query->where('score', 0);
            }
        }
    }

    public function scopeDiscogsReleaseWhereFormats($query, $filterValues)
    {
        if (isset($filterValues['formats']) and count($filterValues['formats']) > 0) {
            $having = ' (';
            foreach ($filterValues['formats'] as $key => $format) {
                if ($key == array_key_last($filterValues['formats'])) {
                    $having .= 'find_in_set ("' . $format . '", format_id) OR ';
                    $having .= 'find_in_set ("' . $format . '", format_parent_id) ';
                } else {
                    $having .= 'find_in_set ("' . $format . '", format_id) OR ';
                    $having .= 'find_in_set ("' . $format . '", format_parent_id) OR ';
                }
            }
            $having .= ')';

            return $query->havingRaw($having);
        }
    }
}
