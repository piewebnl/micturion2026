<?php

namespace Database\Seeders;

use DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class ConcertItemSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'concert_items';
        $this->file = '/database/seeders/csvs/ConcertItems.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
