<?php

namespace App\Console\Commands\Wishlist;

use App\Models\Wishlist\WishlistAlbum;
use App\Services\CsvImport\CsvReader;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

// php artisan command:WishlistAlbumLocalDbSync
class WishlistAlbumLocalDbSyncCommand extends Command
{
    protected $signature = 'command:WishlistAlbumLocalDbSync';

    private string $channel = 'wishlist_albums_local_db_sync';

    public function handle()
    {

        if (App::environment() != 'local') {
            return;
        }

        Logger::deleteChannel($this->channel);

        Logger::echoChannel($this->channel);

        DB::table('wishlist_albums')->truncate();

        $source = ltrim(config('music.wishlist_albums_local_csv'), '/');

        $csvReader = new CsvReader;
        $rows = $csvReader->read($source);

        foreach ($rows as $row) {

            WishlistAlbum::updateOrCreate(
                [
                    'persistent_album_id' => $row['persistent_album_id'],
                ],
                [
                    'format' => $row['format'],
                    'notes' => $row['notes'],
                ]
            );
        }

        // Logger::echo($this->channel);
    }
}
