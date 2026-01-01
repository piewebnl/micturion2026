<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class TiermakerArtistSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'tiermaker_artists';
        $this->file = '/database/seeders/csvs/TiermakerArtists.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
