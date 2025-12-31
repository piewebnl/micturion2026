<?php

namespace App\Services\Concert;

use App\Models\Concert\Concert;
use App\Models\Concert\ConcertImage;
use App\Models\Concert\ConcertItem;

// Create concert image (via upload or found on disk)
class ConcertImageCreator
{
    private // $response;

    private string $channel = 'concert_create_images';

    public function createConcertImage(int $id)
    {

        $concertItem = ConcertItem::with('Concert', 'ConcertArtist')->find($id);

        if ($concertItem) {

            $concertImage = new ConcertImage;
            $concertImage->create($concertItem);
        }
    }
}
