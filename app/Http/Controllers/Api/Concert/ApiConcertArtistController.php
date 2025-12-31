<?php

namespace App\Http\Controllers\Api\Concert;

use App\Http\Controllers\Controller;
use App\Models\Concert\ConcertArtist;
use Illuminate\Http\JsonResponse;

class ApiConcertArtistController extends Controller
{
    public function index(): JsonResponse
    {
        $venues = ConcertArtist::all();

        return response()->json($venues);
    }
}
