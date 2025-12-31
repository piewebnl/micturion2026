<?php

namespace App\Http\Controllers\Api\Tiermaker;

use App\Http\Controllers\Controller;
use App\Models\Tiermaker\TiermakerArtist;
use Illuminate\Http\JsonResponse;

class ApiTiermakerArtistsController extends Controller
{
    public function index(): JsonResponse
    {
        $tiermakerArtists = TiermakerArtist::all();

        return response()->json($tiermakerArtists);
    }
}
