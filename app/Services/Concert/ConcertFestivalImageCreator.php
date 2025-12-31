<?php

namespace App\Services\Concert;

use App\Models\Concert\ConcertFestival;
use App\Models\Concert\ConcertFestivalImage;

// Create concert festival image (via upload or found on disk)
class ConcertFestivalImageCreator
{
    public function createConcertFestivalImage(int $id)
    {

        $concertFestival = ConcertFestival::with('Concert')->find($id);

        if ($concertFestival) {

            $concertImage = new ConcertFestivalImage;
            $concertImage->create($concertFestival);
        }
    }
}
