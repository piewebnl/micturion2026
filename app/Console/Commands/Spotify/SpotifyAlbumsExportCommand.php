<?php

namespace App\Console\Commands\Spotify;

use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\Spotify\Deleters\SpotifyAlbumsDeleter;
use App\Services\Spotify\Exporters\SpotifyAlbumsExporter;
use App\Services\SpotifyApi\Getters\SpotifyApiUserAlbumsGetter;

// php artisan command:SpotifyAlbumsExport
class SpotifyAlbumsExportCommand extends Command
{
    protected $signature = 'command:SpotifyAlbumsExport';

    protected $description = 'Export all iTunes album to Spotify albums via its api and delete unwanted';

    private string $channel = 'spotify_albums_export';

    private int $perPage = 50;

    private $api;

    private $exportedIds = [];


    public function handle()
    {
        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $this->api = (new SpotifyApiConnect($this))->getApi();

        if (!$this->api) {
            return self::FAILURE;
        }

        $this->exportAlbums();
        $this->deleteUnwanted();
    }


    private function exportAlbums(): void
    {
        $spotifyAlbumsExporter = new SpotifyAlbumsExporter($this->api, $this->perPage, $this);
        $lastPage = $spotifyAlbumsExporter->getLastPage();

        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            $exportedIds = $spotifyAlbumsExporter->export($page);
            if (!empty($exportedIds)) {
                $this->exportedIds = array_merge($this->exportedIds, $exportedIds);
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }

    private function deleteUnwanted()
    {

        // Get online Ids        
        $spotifyApiUserAlbumsGetter = new SpotifyApiUserAlbumsGetter($this->api, $this->perPage);
        $lastPage = $spotifyApiUserAlbumsGetter->getLastPage();

        $ids = [];
        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyApiUserAlbumsGetter = new SpotifyApiUserAlbumsGetter($this->api, $this->perPage);
            $ids = array_merge($ids, $spotifyApiUserAlbumsGetter->getPerPage($page));
        }


        $spotifyAlbumsExporter = new SpotifyAlbumsExporter($this->api, $this->perPage, $this);
        $all = $spotifyAlbumsExporter->getAlbums();
        $allAlbums = $all->pluck('spotify_api_album_id')->toArray();

        $eraseFromSpotify = array_diff($ids, $allAlbums);
        if (empty($eraseFromSpotify)) {
            return;
        }

        $spotifyAlbumsDeleter = new SpotifyAlbumsDeleter($this->api, $eraseFromSpotify);
        $lastPage = $spotifyAlbumsDeleter->getLastPage();


        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyAlbumsDeleter->delete($page);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
