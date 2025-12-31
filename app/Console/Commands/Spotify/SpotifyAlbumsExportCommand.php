<?php

namespace App\Console\Commands\Spotify;

use App\Jobs\Spotify\SpotifyAlbumsExportJob;
use App\Services\Spotify\Deleters\SpotifyAlbumsDeleter;
use App\Services\Spotify\Exporters\SpotifyAlbumsExporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\SpotifyApi\Getters\SpotifyApiUserAlbumsGetter;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:SpotifyAlbumsExport
class SpotifyAlbumsExportCommand extends Command
{
    protected $signature = 'command:SpotifyAlbumsExport';

    private string $channel;

    private int $perPage = 50;

    private $api;

    private $totalAlbumsToExport = 0;

    private $albumsExported = [];

    private array $fetchedUserAlbumIds;

    public function handle()
    {
        $this->channel = 'spotify_albums_export';

        Logger::deleteChannel($this->channel);

        $api = (new SpotifyApiConnect)->getApi();
        if (!$api) {
            Logger::log('error', $this->channel, 'No valid spotify API connection');
            Logger::echo($this->channel);

            return;
        }

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }

        $this->api = (new SpotifyApiConnect)->getApi();
        $this->export();
        $this->deleteUnwanted();

        Logger::echo($this->channel);
    }

    private function export()
    {
        $spotifyAlbumsExporter = new SpotifyAlbumsExporter($this->api, $this->perPage);
        $lastPage = $spotifyAlbumsExporter->getLastPage();
        $this->totalAlbumsToExport = $spotifyAlbumsExporter->getTotal();

        if ($this->totalAlbumsToExport == 0) {
            Logger::log('info', $this->channel, 'No albums to export');

            return;
        }

        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            SpotifyAlbumsExportJob::dispatch(
                $page,
                $this->perPage

            );
            sleep(0.5);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }

    // JOB!!
    private function deleteUnwanted()
    {

        if (!$this->totalAlbumsToExport) {
            return;
        }

        $this->fetchAllUserAlbums();

        $spotifyAlbumsExporter = new SpotifyAlbumsExporter($this->api, $this->perPage);
        $all = $spotifyAlbumsExporter->getAlbums();
        $allAlbums = $all->pluck('spotify_api_album_id')->toArray();

        $eraseFromSpotfy = array_diff($this->fetchedUserAlbumIds, $allAlbums);
        $spotifyAlbumsDeleter = new SpotifyAlbumsDeleter($this->api, $eraseFromSpotfy);
        $lastPage = $spotifyAlbumsDeleter->getLastPage();

        // Job!!
        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyAlbumsDeleter->delete($page);
            $this->output->progressAdvance();
            sleep(0.5);
        }

        $this->output->progressFinish();
    }

    // SERVICE?
    private function fetchAllUserAlbums()
    {

        $spotifyApiUserAlbumsGetter = new SpotifyApiUserAlbumsGetter($this->api, $this->perPage);
        $lastPage = $spotifyApiUserAlbumsGetter->getLastPage();

        $this->output->progressStart($lastPage);

        $ids = [];
        // Cannot be done via job
        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyApiUserAlbumsGetter = new SpotifyApiUserAlbumsGetter($this->api, $this->perPage);
            $ids = array_merge($ids, $spotifyApiUserAlbumsGetter->getPerPage($page));
            sleep(0.5);
            $this->output->progressAdvance();
        }

        $this->fetchedUserAlbumIds = $ids;
        $this->output->progressFinish();
    }
}
