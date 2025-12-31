<?php

namespace App\Console\Commands\Wishlist;

use App\Models\Setting;
use App\Models\Wishlist\MusicStore;
use App\Models\Wishlist\WishlistAlbum;
use App\Models\Wishlist\WishlistAlbumPrice;
use App\Services\CsvImport\CsvCreator;
use App\Services\CsvImport\CsvReader;
use App\Services\Ftp\FtpUploader;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:WishlistAlbumPricesLocalToLive
class WishlistAlbumPricesLocalToLiveCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:WishlistAlbumPricesLocalToLive';

    private string $channel = 'wishlist_albums_prices_local_to_live';

    public function handle()
    {
        if (App::environment() != 'local') {
            return;
        }

        $musicStoreId = MusicStore::where('key', 'MEDIMOPS')->pluck('id')->first();

        // TO SERVICE
        $wishlistAlbumPrices = WishlistAlbumPrice::select(
            'wishlist_albums.persistent_album_id as persistent_album_id',
            'music_stores.key as music_store_key',
            'wishlist_album_prices.url as url',
            'wishlist_album_prices.score as score',
            'wishlist_album_prices.format as format',
            'wishlist_album_prices.price as price'
        )->leftJoin('wishlist_albums', 'wishlist_albums.id', '=', 'wishlist_album_prices.wishlist_album_id')
            ->leftJoin('music_stores', 'music_stores.id', '=', 'wishlist_album_prices.music_store_id')
            ->where('music_store_id', $musicStoreId)->get()->toArray();

        $lastPage = count($wishlistAlbumPrices);

        Logger::deleteChannel($this->channel);

        // Make a csv and push to live
        if (App::environment() == 'local') {

            Logger::echoChannel($this->channel);

            $csvFile = ltrim(config('music.wishlist_album_prices_local_csv'), '/');

            $csvCreator = new CsvCreator;
            $csvCreator->create($csvFile, $wishlistAlbumPrices, [
                'persistent_album_id',
                'music_store_key',
                'url',
                'score',
                'format',
                'price',
            ]);

            // Write to FTP
            $dest = config('music.ftp_wishlist_album_prices_local_csv');
            $ftpUploader = new FtpUploader;
            $ftpUploader->upload($csvFile, $dest, $this->channel);
        }

        // Read into the dbase
        if (App::environment() != 'local') {

            $source = ltrim(config('music.wishlist_album_prices_local_csv'), '/');
            $csvTimestamp = filemtime($source);

            $lastSync = Setting::getSetting('wishlist_album_prices_last_sync');
            $lastSyncTimestamp = $lastSync ? strtotime($lastSync) : null;

            echo "db sync: {$lastSyncTimestamp} vs csv data: {$csvTimestamp}";

            if (!$lastSyncTimestamp || $lastSyncTimestamp !== $csvTimestamp) {

                echo ' -> sync';

                Logger::echoChannel($this->channel);

                $csvReader = new CsvReader;
                $rows = $csvReader->read($source);

                foreach ($rows as $index => $row) {

                    $persistentAlbumId = $row['persistent_album_id'];
                    $musicStoreKey = $row['music_store_key'];

                    $musicStoreId = MusicStore::where('key', $musicStoreKey)->pluck('id')->first();
                    $wishlistAlbumId = WishlistAlbum::where('persistent_album_id', $persistentAlbumId)->pluck('id')->first();

                    if ($musicStoreId && $wishlistAlbumId) {
                        WishlistAlbumPrice::updateOrCreate(
                            [
                                'wishlist_album_id' => $wishlistAlbumId,
                                'music_store_id' => $musicStoreId,
                            ],
                            [
                                'url' => $row['url'],
                                'score' => $row['score'],
                                'format' => $row['format'],
                                'price' => $row['price'],
                            ]
                        );
                    }
                }

                // Store the timestamp as datetime again
                Setting::addSetting(
                    'wishlist_album_prices_last_sync',
                    date('Y-m-d H:i:s', $csvTimestamp)
                );
            } else {
                dd('nothing to sync...');
            }

            $this->clearCache('get-wishlist-albums-with-prices');
        }

        Logger::echo($this->channel);
    }
}
