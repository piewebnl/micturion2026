<?php

namespace App\Models\Concert;

use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class ConcertVenue extends Model
{
    use QueryCache;

    protected $guarded = [];

    public function concerts()
    {
        return $this->hasMany(Concert::class);
    }

    public function getAllConcertVenues()
    {

        $concertVenues = $this->getCache('get-all-concert-venues');

        if (!$concertVenues) {
            $concertVenues = ConcertVenue::groupBy('name')->orderBy('name')->get();
            $this->setCache('get-all-concert-venues', [], $concertVenues);
        }

        return $concertVenues;
    }
}
