<?php

namespace Database\Seeders;

use DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class ConcertSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'concerts';
        $this->file = '/database/seeders/csvs/Concerts.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
