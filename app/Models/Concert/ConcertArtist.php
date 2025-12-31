<?php

namespace App\Models\Concert;

use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class ConcertArtist extends Model
{
    use GlobalScopesTrait;
    use QueryCache;

    protected $guarded = [];

    public function storeOrUpdate(ConcertArtist $concertArtist): ConcertArtist
    {
        return ConcertArtist::UpdateOrCreate(
            ['id' => $concertArtist->id],
            [
                'name' => $concertArtist->name,
            ]
        );
    }

    public function getAllConcertArtists()
    {

        $concertArtists = $this->getCache('get-all-concert-artists');

        if (!$concertArtists) {
            $concertArtists = ConcertArtist::groupBy('name')->get(); // collection;
            $this->setCache('get-all-concert-artists', [], $concertArtists);
        }

        return $concertArtists;
    }
}
