<?php

namespace App\Models\Concert;

use App\Scopes\ConcertScopesTrait;
use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use GlobalScopesTrait;
    use ConcertScopesTrait;
    use QueryCache;

    protected $guarded = [];

    public function concertVenue()
    {
        return $this->belongsTo(ConcertVenue::class);
    }

    public function concertFestival()
    {
        return $this->belongsTo(ConcertFestival::class);
    }

    public function concertItems()
    {
        return $this->hasMany(ConcertItem::class)->orderBy('order');
    }

    public function concertArtists()
    {
        return $this->hasManyThrough(ConcertItem::class, ConcertArtist::class, 'id', 'concert_id', 'id', 'id')->orderBy('order');
    }

    public function getConcerts(array $filterValues, $skipGetCache = false)
    {

        $concerts = [];

        if (!$skipGetCache) {
            $concerts = $this->getCache('get-concerts', $filterValues);
        }

        if (!$concerts) {
            $concertItemsOrder = function ($query) use ($filterValues) {
                if (($filterValues['sort'] ?? null) !== 'artist') {
                    return $query->orderBy('order', 'ASC');
                }

                return $query
                    ->leftJoin('concert_artists', 'concert_artists.id', '=', 'concert_items.concert_artist_id')
                    ->orderBy('concert_artists.name', 'ASC')
                    ->orderBy('concert_items.order', 'ASC')
                    ->select('concert_items.*');
            };

            $withRelations = [
                'concertVenue',
                'concertFestival.ConcertFestivalImage',
                'concertItems' => $concertItemsOrder,
            ];

            $concertsQuery = Concert::with($withRelations)
                ->ConcertWhereName($filterValues)
                ->ConcertWhereYear($filterValues)
                ->whereId($filterValues, 'concerts.concert_venue_id', 'venue')
                ->whereId($filterValues, 'concerts.concert_festival_id', 'festival')
                ->ConcertWhereKeyword($filterValues);

            $sort = $filterValues['sort'] ?? null;
            if ($sort === 'date') {
                $concertsQuery->orderBy('concerts.date', $filterValues['order'] ?? 'desc');
            } elseif ($sort === 'artist') {
                $order = $filterValues['order'] ?? 'asc';
                $concertsQuery->orderByRaw(
                    "(select min(concert_artists.name)
                      from concert_items
                      left join concert_artists on concert_artists.id = concert_items.concert_artist_id
                      where concert_items.concert_id = concerts.id
                        and (concert_items.support = 0 or concert_items.support is null)) {$order}"
                );
            } else {
                $concertsQuery->sortAndOrderBy($filterValues);
            }

            $concerts = $concertsQuery->customPaginateOrLimit($filterValues);

            $this->setCache('get-concerts', $filterValues, $concerts);
        }

        return $concerts;
    }

    public function getAllConcertYears()
    {

        $concertYears = $this->getCache('get-all-concert-years');

        if (!$concertYears) {
            $concertYears = Concert::selectRaw('substr(date,1,4) as years')->groupBy('years')->orderBy('years', 'desc')->get();
            $this->setCache('get-all-concert-years', [], $concertYears);
        }

        return $concertYears;
    }
}
