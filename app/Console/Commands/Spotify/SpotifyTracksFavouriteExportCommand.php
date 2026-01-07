<?php

namespace App\Console\Commands\Spotify;

use App\Jobs\Spotify\SpotifyTracksFavouriteExportJob;
use App\Services\Logger\Logger;
use App\Services\Spotify\Deleters\SpotifyTracksFavouriteDeleter;
use App\Services\Spotify\Exporters\SpotifyTracksFavouriteExporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\SpotifyApi\Getters\SpotifyApiUserFavouriteTracksGetter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:SpotifyTracksFavouriteExport
class SpotifyTracksFavouriteExportCommand extends Command
{
    protected $signature = 'command:SpotifyTracksFavouriteExport';

    protected $description = 'Exports liked songs from iTunes to Spotify liked songs';

    private string $channel;

    private int $perPage = 50;

    private $api;

    private $tracksExported = [];

    private array $fetchedUserFavouriteTrackIds;

    public function handle()
    {
        $this->channel = 'spotify_tracks_favourite_export';

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

        $this->api = (new SpotifyApiConnect)->getApi();
        if (!$this->api) {
            // Logger::echo($this->channel);

            return;
        }

        $this->export();

        $this->fetch();

        $this->deleteUnwanted();

        // Logger::echo($this->channel);
    }

    private function export()
    {
        $spotifyTracksFavouriteExporter = new SpotifyTracksFavouriteExporter($this->api, $this->perPage);
        $lastPage = $spotifyTracksFavouriteExporter->getLastPage();
        $totalTracksFavourite = $spotifyTracksFavouriteExporter->getTotal();

        if (!$totalTracksFavourite) {
            Logger::log('warning', $this->channel, 'No favourite tracks to export (No fave songs iTunes or no spotify match yet?');

            return;
        }

        // Keep these
        $all = $spotifyTracksFavouriteExporter->getTracksFavourite();
        $this->tracksExported = $all->pluck('spotify_api_track_id')->toArray();

        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            SpotifyTracksFavouriteExportJob::dispatch(
                $page,
                $this->perPage

            );
            // sleep(0.5);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }

    private function fetch()
    {

        $spotifyApiUserFavouriteTracksGetter = new SpotifyApiUserFavouriteTracksGetter($this->api, $this->perPage);
        $lastPage = $spotifyApiUserFavouriteTracksGetter->getLastPage();

        $this->output->progressStart($lastPage);

        $ids = [];
        // Cannot be done via job
        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyApiUserFavouriteTracksGetter = new SpotifyApiUserFavouriteTracksGetter($this->api, $this->perPage);
            $ids = array_merge($ids, $spotifyApiUserFavouriteTracksGetter->getPerPage($page));
            // sleep(0.5);
            $this->output->progressAdvance();
        }

        $this->fetchedUserFavouriteTrackIds = $ids;

        $this->output->progressFinish();
    }

    private function deleteUnwanted()
    {

        $eraseFromSpotfy = array_diff($this->fetchedUserFavouriteTrackIds, $this->tracksExported);

        // dd($eraseFromSpotfy);

        $spotifyTracksFavouriteDeleter = new SpotifyTracksFavouriteDeleter($this->api, $eraseFromSpotfy);
        $lastPage = $spotifyTracksFavouriteDeleter->getLastPage();

        // Job!!
        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyTracksFavouriteDeleter->delete($page);
            $this->output->progressAdvance();
            // sleep(0.5);
        }

        $this->output->progressFinish();
    }
}
