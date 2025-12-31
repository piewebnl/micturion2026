<?php

namespace Database\Seeders;

use DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class DiscogsReleaseCustomIdSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'discogs_release_custom_ids';
        $this->file = '/database/seeders/csvs/DiscogsReleaseCustomIds.csv';
        $this->truncate = false;
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
