<?php

namespace App\Console\Commands\Spotify;

use App\Services\Spotify\Deleters\SpotifyAlbumsDeleter;
use App\Services\Spotify\Exporters\SpotifyAlbumsExporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\SpotifyApi\Getters\SpotifyApiUserAlbumsGetter;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:SpotifyAlbumsExport
class SpotifyAlbumsExportCommand extends Command
{
    protected $signature = 'command:SpotifyAlbumsExport';

    protected $description = 'Export all iTunes album to Spotify albums via its api';

    private string $channel = 'spotify_albums_export';

    private int $perPage = 50;

    private $api;

    private $totalAlbumsToExport = 0;

    private array $fetchedUserAlbumIds = [];

    public function handle()
    {
        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $this->api = (new SpotifyApiConnect($this))->getApi();

        if (!$this->api) {
            return self::FAILURE;
        }

        $this->exportAlbums();
        //$this->deleteUnwanted();

        // Logger::echo($this->channel);
    }

    private function exportAlbums()
    {
        $spotifyAlbumsExporter = new SpotifyAlbumsExporter($this->api, $this->perPage);
        $lastPage = $spotifyAlbumsExporter->getLastPage();

        $this->totalAlbumsToExport = $spotifyAlbumsExporter->getTotal();
        if ($this->totalAlbumsToExport == 0) {
            Logger::log('error', $this->channel, 'No albums to export', [], $this);
            return;
        }

        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyAlbumsExporter->export($page);
            sleep(1);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }

    /*
    private function deleteUnwanted()
    {

        if (!$this->totalAlbumsToExport) {
            return;
        }

        $this->fetchAllUserAlbums();

        $spotifyAlbumsExporter = new SpotifyAlbumsExporter($this->api, $this->perPage);
        $all = $spotifyAlbumsExporter->getAlbums();
        $allAlbums = $all->pluck('spotify_api_album_id')->toArray();

        $eraseFromSpotify = array_diff($this->fetchedUserAlbumIds, $allAlbums);
        if (empty($eraseFromSpotify)) {
            return;
        }

        $spotifyAlbumsDeleter = new SpotifyAlbumsDeleter($this->api, $eraseFromSpotify);
        $lastPage = $spotifyAlbumsDeleter->getLastPage();


        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyAlbumsDeleter->delete($page);
            $this->output->progressAdvance();
            sleep(0.5);
        }

        $this->output->progressFinish();
    }
        */
}
