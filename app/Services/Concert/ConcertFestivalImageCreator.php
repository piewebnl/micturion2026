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

        $status = false;
        if ($concertFestival) {

            $concertImage = new ConcertFestivalImage;
            $status = $concertImage->create($concertFestival);
        }

        return $status;
    }
}
