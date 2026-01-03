<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Jobs\ItunesLibrary\ItunesLibraryImportPlaylistTracksJob;
use App\Models\ItunesLibrary\ItunesLibrary;
use App\Models\Playlist\Playlist;
use App\Services\ItunesLibrary\ItunesLibraryPlaylistTracksImporter;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:ItunesLibraryImportTracksFavourite
class ItunesLibraryImportTracksFavouriteCommand extends Command
{
    protected $signature = 'command:ItunesLibraryImportTracksFavourite';

    private string $channel;

    private int $perPage = 1000;

    public function handle()
    {
        $this->channel = 'itunes_library_import_tracks_favourite';

        Logger::deleteChannel($this->channel);

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }

        $playlist = new Playlist;
        $playlist->fill([
            'name' => 'Favourite Tracks',
            'persistent_id' => config('ituneslibrary.itunes_tracks_favourite_playlist_persistent_id'),
        ]);

        $itunesLibrary = new ItunesLibrary;
        $itunesLibrary->getItunesLibrary();

        $tracksFavouriteImporter = new ItunesLibraryPlaylistTracksImporter($itunesLibrary, $playlist, $this->perPage);
        if ($tracksFavouriteImporter->getTotal() == 0) {
            Logger::log('error', $this->channel, 'iTunes library playlist has no tracks: ' . $playlist->name);
            // Logger::echo($this->channel);

            return;
        }
        $lastPage = $tracksFavouriteImporter->getLastPage();

        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            ItunesLibraryImportPlaylistTracksJob::dispatchSync(
                $tracksFavouriteImporter,
                [
                    'page' => $page,
                    'per_page' => $this->perPage,
                    'playlist' => $playlist,
                    'import_favourite' => true,
                ]
            );
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
