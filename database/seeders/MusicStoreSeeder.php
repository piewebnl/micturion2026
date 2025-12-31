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

        /*
        DB::table('music_stores')->insert(
            [
                'id' => '1',
                'key' => 'AMAZONNL',
                'name' => 'Amazon NL',
                'url' => 'https://www.amazon.nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        DB::table('music_stores')->insert(
            [
                'id' => '2',
                'key' => 'RECVINYL',
                'name' => 'Records On Vinyl',
                'url' => 'https://www.recordsonvinyl.nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        DB::table('music_stores')->insert(
            [
                'id' => '3',
                'key' => 'PLATO',
                'name' => 'PlatoMania',
                'url' => 'https://www.platomania.nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        DB::table('music_stores')->insert(
            [
                'id' => '4',
                'key' => 'SOUNDS',
                'name' => 'Sounds',
                'url' => 'https://www.sounds.nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        DB::table('music_stores')->insert(
            [
                'id' => '5',
                'key' => 'FEVER',
                'name' => 'Vinyl Fever',
                'url' => 'https://vinylfever.com',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        DB::table('music_stores')->insert(
            [
                'id' => '6',
                'key' => 'LARGE',
                'name' => 'Large',
                'url' => 'https://www.large.nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        DB::table('music_stores')->insert(
            [
                'id' => '7',
                'key' => 'KROESE',
                'name' => 'Kroese Online',
                'url' => 'https://www.kroese-online.nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        DB::table('music_stores')->insert(
            [
                'id' => '8',
                'key' => 'IMUSIC',
                'name' => 'iMusic',
                'url' => 'https://www.imusic.nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        */
    }
}
