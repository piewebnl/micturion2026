<?php

namespace App\Console\Commands\Wishlist;

use App\Jobs\Wishlist\WishlistAlbumPricesScrapeJob;
use App\Models\Wishlist\MusicStore;
use App\Models\Wishlist\WishlistAlbum;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:WishlistAlbumPricesScrape
class WishlistAlbumPricesScrapeCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:WishlistAlbumPricesScrape';

    private string $channel = 'wishlist_albums_scrape_prices';

    public function handle()
    {

        $wishlistAlbums = (new WishlistAlbum)->getWishlistAlbums([]);
        $lastPage = count($wishlistAlbums);

        Logger::deleteChannel($this->channel);
        Logger::deleteChannel('html_scraper');

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }

        $musicStores = MusicStore::all();
        if ($musicStores->count() == 0) {
            Logger::log('error', $this->channel, 'No music stores found. Please seed.');
            Logger::echo($this->channel);

            return;
        }

        if ($lastPage > 0) {

            $this->output->progressStart($lastPage);

            for ($page = 1; $page <= $lastPage; $page++) {
                WishlistAlbumPricesScrapeJob::dispatchSync($page, 1);
                $this->output->progressAdvance();
            }

            Logger::log('info', $this->channel, $lastPage . ' wishlist albums scraped');

            $this->output->progressFinish();
        } else {
            Logger::log('info', $this->channel, 'No wishlist albums or iTunes library not imported');
        }

        $this->clearCache('get-wishlist-albums-with-prices');

        Logger::echo($this->channel);
    }
}
