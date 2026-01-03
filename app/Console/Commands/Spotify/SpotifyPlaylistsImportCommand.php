<?php

namespace App\Console\Commands\Spotify;

use App\Jobs\Spotify\SpotifyPlaylistImportJob;
use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Spotify\Importers\SpotifyPlaylistsImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;

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
        if (!$lastPage) {
            return self::SUCCESS;
        }

        $oldPlaylistIds = SpotifyPlaylist::pluck('spotify_api_playlist_id')->filter()->values()->all();

        $spotifyPlaylistImporter = new SpotifyPlaylistsImporter($api, $this->perPage);

        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            $spotifyPlaylistImporter->import($page);
            $this->output->progressAdvance();
        }

        $spotifyPlaylistImporter->deleteOldPlaylists($oldPlaylistIds);


        $this->output->progressFinish();

        return self::SUCCESS;
    }
}
