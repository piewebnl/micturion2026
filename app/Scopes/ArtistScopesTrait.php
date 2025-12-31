<?php

namespace App\Scopes;

trait ArtistScopesTrait
{
    public function scopeArtistWhereKeyword($query, $filterValues, $keywordSearchIds)
    {

        // Search trough a lot of fields
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('artists.name', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orwhere('albums.name', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orwhere('albums.year', 'LIKE', '%' . $filterValues['keyword'] . '%')
                ->orWhereIn('albums.id', $keywordSearchIds);
        }
    }

    public function scopeArtistWhereArtist($query, $filterValues)
    {

        if (isset($filterValues['artist']) and $filterValues['artist'] != '') {
            return $query->where('artists.id', $filterValues['artist']);
        }
    }

    public function scopeArtistWhereName($query, $filterValues)
    {

        if (isset($filterValues['name']) and $filterValues['name'] != '') {
            return $query->where('artists.name', $filterValues['name']);
        }
    }

    public function scopeArtistWhereFormats($query, $filterValues)
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

    public function scopeArtistWhereLetter($query, $filterValues)
    {
        if (isset($filterValues['start_letter']) and $filterValues['start_letter'] != null) {
            return $query->where('artists.sort_name', 'REGEXP', '^[' . $filterValues['start_letter'] . '-z0-9]');
        }
    }

    public function scopeArtistWhereSpineImagesChecked($query, $filterValues)
    {
        if (isset($filterValues['view']) and $filterValues['view'] == 'spines') {

            if (isset($filterValues['spine_images_checked']) and $filterValues['spine_images_checked'] == 'both') {
                return $query->where('spine_images.checked', 0)->orwhere('spine_images.checked', 1);
            }
            if (isset($filterValues['spine_images_checked']) and $filterValues['spine_images_checked'] == true) {
                return $query->where('spine_images.checked', 1);
            }
            if (isset($filterValues['spine_images_checked']) and $filterValues['spine_images_checked'] == false) {
                return $query->where('spine_images.checked', 0);
            }
        }
    }

    public function scopeArtistOrderBy($query, $filterValues)
    {
        /*
        if (!isset($filterValues['sort'])) {
            return;
        }
            */

        if (!isset($filterValues['sort']) or $filterValues['sort'] == 'artist') {

            // default artist
            $order = 'asc';
            if (isset($filterValues['order'])) {
                $order = $filterValues['order'];
            }

            return $query->orderBy('artists.sort_name', $order)
                ->orderBy('categories.id')
                ->orderBy('albums.sort_name');
        }

        if ($filterValues['sort'] == 'random') {
            return $query->inRandomOrder();
        }

        return $query->orderBy($filterValues['sort'], $filterValues['order']);
    }

    public function scopeArtistIncludeCompilations($query, $filterValues)
    {
        if (isset($filterValues['compilations']) and $filterValues['compilations'] == true) {
            return $query;
        }

        return $query->where('is_compilation', '=', null);
    }

    public function scopeArtistWhereCategoriesAndSongs($query, $filterValues)
    {

        if (isset($filterValues['categories'])) {

            if (count($filterValues['categories']) > 0) {
                if (isset($filterValues['songs']) && $filterValues['songs']) {
                    array_push($filterValues['categories'], '7');
                }

                return $query->whereIn('category_id', $filterValues['categories']);
            }

            if (count($filterValues['categories']) == 0) {
                if (isset($filterValues['songs']) && $filterValues['songs']) {
                    array_push($filterValues['categories'], 1, 2, 3, 4, 5, 6, 7);

                    return $query->whereIn('category_id', $filterValues['categories']);
                } else {
                    array_push($filterValues['categories'], 1, 2, 3, 4, 5, 6);

                    return $query->whereIn('category_id', $filterValues['categories']);
                }
            }
        }
    }

    public function scopeArtistWhereNoAlbumArtwork($query, $filterValues)
    {

        if (isset($filterValues['view']) and $filterValues['view'] == 'noartwork') {
            return $query->whereNull('artwork_made')->orWhere('artwork_made', 0);
        }
    }
}
