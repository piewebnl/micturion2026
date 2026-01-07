<?php

namespace App\Scopes;

trait WishlistAlbumScopesTrait
{
    public function scopeWishlistAlbumSortAndOrderBy($query, $filterValues)
    {
        return $query->orderBy($filterValues['sort'], $filterValues['order'])
            ->orderBy('album_sort_name')
            ->orderBy('price');
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
}
