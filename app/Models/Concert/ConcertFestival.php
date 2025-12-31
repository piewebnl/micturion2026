<?php

namespace App\Models\Concert;

use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class ConcertFestival extends Model
{
    use GlobalScopesTrait;
    use QueryCache;

    protected $guarded = [];

    public function concert()
    {
        return $this->hasOne(Concert::class);
    }

    public function concertFestivalImage()
    {
        return $this->hasOne(ConcertFestivalImage::class);
    }

    public function storeImages(ConcertFestival $concertFestival, array $images = [])
    {
        foreach ($images as $image) {
            $imageCreator = new ConcertFestivalImage;
            $imageCreator->create($concertFestival, $image->getPath() . '/' . $image->getFilename());
        }
    }

    public function getAllConcertFestivals()
    {

        $concertVenues = $this->getCache('get-all-concert-festivals');

        if (!$concertVenues) {
            $concertVenues = ConcertFestival::orderBy('name')->get();
            $this->setCache('get-all-concert-festivals', [], $concertVenues);
        }

        return $concertVenues;
    }
}
