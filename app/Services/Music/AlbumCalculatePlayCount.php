<?php

namespace App\Services\Music;

use App\Models\Music\Album;
use App\Models\Music\Song;

class AlbumCalculatePlayCount
{
    public function calculate()
    {
        $songs = Song::selectRaw('*, sum(play_count) as album_play_count')->with('album')->groupBy('album_id')->get();

        foreach ($songs as $song) {
            Album::where('id', $song->album->id)->update(['play_count' => $song->album_play_count]);
        }
    }
}
