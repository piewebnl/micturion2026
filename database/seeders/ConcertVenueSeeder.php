<?php

namespace Database\Seeders;

use DB;
use Illuminate\Support\Carbon;
use JeroenZwart\CsvSeeder\CsvSeeder;

class ConcertVenueSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'concert_venues';
        $this->file = '/database/seeders/csvs/ConcertVenues.csv';
        $this->truncate = false;
        $this->parsers = ['date' => function ($value) {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d 00:00:00');
        }];
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
