<?php

namespace App\Services\Concert;

use App\Models\Concert\Concert;
use App\Models\Concert\ConcertImage;
use App\Services\Images\ImageDeleter;
use Illuminate\Http\JsonResponse;

// Create concert image (via upload or found on disk)
class ConcertImageDeleter
{
    private $response;

    public function deleteConcertImage(int $id)
    {

        DD('DEAD');
        $concertImage = ConcertImage::findOrFail($id);

        // delete all from storage
        $imageDeleter = new ImageDeleter('concert');
        $imageDeleter->delete($concertImage['slug']);

        // delete from database
        $concertImage->delete();

        $this->response = response()->deleted('Concert image deleted');
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
