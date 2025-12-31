<?php

namespace App\Http\Controllers\Api\Concert;

use App\Http\Controllers\Controller;
use App\Models\Concert\Concert;
use Illuminate\Http\JsonResponse;

class ApiConcertController extends Controller
{
    public function index(): JsonResponse
    {
        $concerts = Concert::all();

        return response()->json($concerts);
    }
}
