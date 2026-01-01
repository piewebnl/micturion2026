<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class TiermakerAlbumSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'tiermaker_albums';
        $this->file = '/database/seeders/csvs/TiermakerAlbums.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
