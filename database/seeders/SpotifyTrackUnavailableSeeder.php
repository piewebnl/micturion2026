<?php

namespace Database\Seeders;

use DB;
use Illuminate\Support\Carbon;
use JeroenZwart\CsvSeeder\CsvSeeder;

class SpotifyTrackUnavailableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'spotify_tracks_unavailable';
        $this->file = '/database/seeders/csvs/SpotifyTracksUnavailable.csv';
        $this->mapping = ['persistent_id', 'artist', 'album', 'name'];
        $this->truncate = false;
        $this->parsers = ['date' => function ($value) {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d 00:00:00');
        }];
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
