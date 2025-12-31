<?php

namespace App\Models\Concert;

use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use GlobalScopesTrait;
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
            $concerts = Concert::with(
                [
                    'concertVenue',
                    'concertFestival.ConcertFestivalImage',
                    'concertItems' => fn ($query) => $query->orderBy('order', 'ASC'),
                ]
            )
                ->ConcertWhereName($filterValues)
                ->ConcertWhereYear($filterValues)
                ->whereId($filterValues, 'concerts.concert_venue_id', 'venue')
                ->whereId($filterValues, 'concerts.concert_festival_id', 'festival')
                ->ConcertWhereKeyword($filterValues)
                ->sortAndOrderBy($filterValues)
                ->customPaginateOrLimit($filterValues);

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
