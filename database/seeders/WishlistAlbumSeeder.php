<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use JeroenZwart\CsvSeeder\CsvSeeder;

class WishlistAlbumSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->tablename = 'wishlist_albums';
        $this->file = '/database/seeders/csvs/WishlistAlbums.csv';
        $this->truncate = false;
        $this->mapping = ['persistent_album_id'];
    }

    public function run()
    {
        DB::disableQueryLog();
        parent::run();
    }
}
