<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;
use App\Http\Requests\Music\ApiSongByTimeRequest;
use App\Models\Music\Format;
use App\Models\Music\Song;
use Illuminate\Http\JsonResponse;

class ApiSongByTimeController extends Controller
{
    public function index(ApiSongByTimeRequest $request): JsonResponse
    {

        $time = $request->validated()['time'];
        $trackNumber = $request->validated()['track_number'];

        $format = new Format;
        $formatIds = $format->getFormatByName(['CD']);

        $song = new Song;
        $songs = $song->getSongsWithAlbum([
            'format_ids' => $formatIds,
            'time_ms' => $time,
            'track_number' => $trackNumber,
        ]);

        return response()->json($songs);
    }
}
