<?php

namespace App\Services\Concert;

use App\Models\Concert\Concert;
use App\Models\Concert\ConcertImage;
use App\Models\Concert\ConcertItem;

// Create concert image (via upload or found on disk)
class ConcertImageCreator
{
    public function createConcertImage(int $id)
    {

        $concertItem = ConcertItem::with('Concert', 'ConcertArtist')->find($id);

        $status = false;

        if ($concertItem) {

            $concertImage = new ConcertImage;
            $status = $concertImage->create($concertItem);
        }

        return $status;
    }
}
