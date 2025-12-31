<?php

namespace App\Http\Controllers\Api\Discogs;

use App\Http\Controllers\Controller;
use App\Models\Discogs\DiscogsReleaseCustomId;
use Illuminate\Http\JsonResponse;

class ApiDiscogsReleaseCustomIdsController extends Controller
{
    public function index(): JsonResponse
    {
        $discogs = DiscogsReleaseCustomId::all();

        return response()->json($discogs);
    }
}
