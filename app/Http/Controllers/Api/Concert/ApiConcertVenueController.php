<?php

namespace App\Http\Controllers\Api\Concert;

use App\Http\Controllers\Controller;
use App\Models\Concert\ConcertVenue;
use Illuminate\Http\JsonResponse;

class ApiConcertVenueController extends Controller
{
    public function index(): JsonResponse
    {
        $venues = ConcertVenue::all();

        return response()->json($venues);
    }
}
