<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Models\Music\Album;
use App\Models\Music\AlbumFormats;

class MusicStatsController extends Controller
{
    public function index()
    {
        $albumFormats = new AlbumFormats;
        $albumFormatsOwned = $albumFormats->getOwned();

        $album = new Album;
        $all = $album->getAmountAllPerYear();
        $unique = $album->getAmountUniquePerYear();

        $albumAmountPerYear = [];
        foreach ($all as $item) {
            $albumAmountPerYear[$item['year']]['amount_all'] = $item['amount'];
        }

        foreach ($unique as $item) {
            $albumAmountPerYear[$item['year']]['amount_unique'] = $item['amount'];
        }

        return view(
            'music.music-stats',
            [
                'albumFormatsOwned' => $albumFormatsOwned,
                'albumAmountPerYear' => $albumAmountPerYear,
            ]
        );
    }
}
