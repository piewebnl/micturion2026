<?php

namespace Database\Seeders;

use DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class MusicStoreSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tablename = 'music_stores';
        $this->file = '/database/seeders/csvs/MusicStores.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
