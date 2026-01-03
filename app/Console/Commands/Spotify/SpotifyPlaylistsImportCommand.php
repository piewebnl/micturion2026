<?php

namespace App\Console\Commands\Spotify;

use App\Jobs\Spotify\SpotifyPlaylistImportJob;
use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Spotify\Importers\SpotifyPlaylistsImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:SpotifyPlaylistsImport
class SpotifyPlaylistsImportCommand extends Command
{
    protected $signature = 'command:SpotifyPlaylistsImport';

    private string $channel = 'spotify_playlists_import';

    private int $perPage = 50;

    public function handle()
    {
        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $api = (new SpotifyApiConnect($this))->getApi();

        if (!$api) {
            return self::FAILURE;
        }
        $lastPage = (new SpotifyPlaylistsImporter($api, $this->perPage))->getLastPage();
        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            SpotifyPlaylistImportJob::dispatchSync($page, $this->perPage);
            $this->output->progressAdvance();
        }

        (new SpotifyPlaylist)->deleteAllHasChanged();

        $this->output->progressFinish();

        return self::SUCCESS;
    }
}
