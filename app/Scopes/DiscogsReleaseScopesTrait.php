<?php

namespace App\Scopes;

trait DiscogsReleaseScopesTrait
{
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
