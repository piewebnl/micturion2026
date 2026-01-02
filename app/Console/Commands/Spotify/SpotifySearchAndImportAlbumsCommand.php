<?php

namespace App\Console\Commands\Spotify;

use App\Jobs\Spotify\SpotifyAlbumImportJob;
use App\Models\Music\Album;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:SpotifySearchAndImportAlbums
class SpotifySearchAndImportAlbumsCommand extends Command
{
    protected $signature = 'command:SpotifySearchAndImportAlbums';

    private string $channel;

    private $perPage = 50;

    private $album = [];

    public function handle()
    {
        $this->channel = 'spotify_search_and_import_albums';

        Logger::deleteChannel($this->channel);

        $api = (new SpotifyApiConnect)->getApi();
        if (!$api) {
            Logger::log('error', $this->channel, 'No valid spotify API connection');
            // Logger::echo($this->channel);

            return;
        }

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }

        // Local do all, on production limit to 50
        if (App::environment() == 'local') {
            $this->album = (new Album)->getAlbumsWithoutSpotifyAlbum([
                'page' => 1,
                'per_page' => $this->perPage,
                'categories' => [1],
            ]);
            $this->output->progressStart($this->album->total());
        } else {
            $this->album = (new Album)->getAlbumsWithoutSpotifyAlbum(
                [
                    'limit' => $this->perPage,
                    'categories' => [1],
                ]
            );
            $this->output->progressStart(count($this->album));
        }

        foreach ($this->album as $index => $song) {
            SpotifyAlbumImportJob::dispatchSync(
                $song->id
            );
            if ($index == $this->perPage) {
                sleep(30);
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
