<?php

namespace Database\Seeders;

use DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class ConcertFestivalSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'concert_festivals';
        $this->file = '/database/seeders/csvs/ConcertFestivals.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
