<?php

namespace Database\Seeders;

use DB;
use Illuminate\Support\Carbon;
use JeroenZwart\CsvSeeder\CsvSeeder;

class SpotifyAlbumCustomIdSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'spotify_album_custom_ids';
        $this->file = '/database/seeders/csvs/SpotifyAlbumCustomIds.csv';
        $this->mapping = ['persistent_id', 'spotify_api_album_custom_id', 'artist', 'name'];
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
