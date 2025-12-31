<?php

namespace Database\Seeders;

use DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class MenuSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'menus';
        $this->file = '/database/seeders/csvs/Menus.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
