<?php

namespace App\Http\Controllers\Api\Concert;

use App\Http\Controllers\Controller;
use App\Models\Concert\ConcertFestival;
use Illuminate\Http\JsonResponse;

class ApiConcertFestivalController extends Controller
{
    public function index(): JsonResponse
    {
        $venues = ConcertFestival::all();

        return response()->json($venues);
    }
}
