<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;
use App\Http\Requests\Music\ApiSongWithAlbumRequest;
use App\Models\Music\Song;
use Illuminate\Http\JsonResponse;

class ApiSongWithAlbumController extends Controller
{
    public function index(ApiSongWithAlbumRequest $request): JsonResponse
    {
        $albumId = $request->validated()['album_id'];

        $song = new Song;
        $songs = $song->getSongsWithAlbum([
            'album_id' => $albumId,
        ]);

        return response()->json($songs);
    }
}
