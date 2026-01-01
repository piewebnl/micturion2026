<?php

namespace Database\Seeders;

use DB;
use JeroenZwart\CsvSeeder\CsvSeeder;


class CategorySeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'categories';
        $this->file = '/database/seeders/csvs/Categories.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
