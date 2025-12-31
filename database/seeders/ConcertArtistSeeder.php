<?php

namespace Database\Seeders;

use DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class ConcertArtistSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'concert_artists';
        $this->file = '/database/seeders/csvs/ConcertArtists.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
