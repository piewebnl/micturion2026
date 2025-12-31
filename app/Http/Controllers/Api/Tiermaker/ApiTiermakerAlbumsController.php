<?php

namespace App\Http\Controllers\Api\Tiermaker;

use App\Http\Controllers\Controller;
use App\Models\Tiermaker\TiermakerAlbum;
use Illuminate\Http\JsonResponse;

class ApiTiermakerAlbumsController extends Controller
{
    public function index(): JsonResponse
    {
        $tiermakerAlbums = TiermakerAlbum::all();

        return response()->json($tiermakerAlbums);
    }
}
