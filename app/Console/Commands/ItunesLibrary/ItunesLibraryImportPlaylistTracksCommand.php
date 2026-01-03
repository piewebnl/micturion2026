<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Jobs\ItunesLibrary\ItunesLibraryImportPlaylistTracksJob;
use App\Models\ItunesLibrary\ItunesLibrary;
use App\Models\Playlist\Playlist;
use App\Services\ItunesLibrary\ItunesLibraryPlaylistTracksImporter;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:ItunesLibraryImportPlaylistTracks
class ItunesLibraryImportPlaylistTracksCommand extends Command
{
    protected $signature = 'command:ItunesLibraryImportPlaylistTracks';

    private string $channel;

    private int $perPage = 1000;

    public function handle()
    {
        $this->channel = 'itunes_library_import_playlist_tracks';

        Logger::deleteChannel($this->channel);

        $playlistModel = new Playlist;
        $playlists = $playlistModel->getPlaylists([]);

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }
        $this->output->progressStart(count($playlists));

        $itunesLibrary = new ItunesLibrary;
        $itunesLibrary->getItunesLibrary();

        foreach ($playlists as $playlist) {

            $playlist = Playlist::find($playlist['id']);

            $playlistTracksImporter = new ItunesLibraryPlaylistTracksImporter($itunesLibrary, $playlist, $this->perPage);
            $lastPage = $playlistTracksImporter->getLastPage();

            for ($page = 1; $page <= $lastPage; $page++) {
                ItunesLibraryImportPlaylistTracksJob::dispatchSync(
                    $playlistTracksImporter,
                    [
                        'page' => $page,
                        'per_page' => $this->perPage,
                        'playlist' => $playlist,
                    ]
                );
            }
            $this->output->progressAdvance();
            unset($playlistTracksImporter);
        }
        unset($itunesLibrary);

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
