<?php

namespace App\Http\Controllers\Api\Concert;

use App\Http\Controllers\Controller;
use App\Models\Concert\ConcertItem;
use Illuminate\Http\JsonResponse;

class ApiConcertItemController extends Controller
{
    public function index(): JsonResponse
    {
        $venues = ConcertItem::all();

        return response()->json($venues);
    }
}
