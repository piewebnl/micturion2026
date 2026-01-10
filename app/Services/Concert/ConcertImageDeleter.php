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

        public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
