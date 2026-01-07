<?php

namespace App\Scopes;

trait ConcertScopesTrait
{
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
}
