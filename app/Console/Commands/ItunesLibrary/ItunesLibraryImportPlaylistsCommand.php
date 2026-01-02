<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Jobs\ItunesLibrary\ItunesLibraryImportPlaylistsJob;
use App\Models\ItunesLibrary\ItunesLibrary;
use App\Services\ItunesLibrary\ItunesLibraryPlaylistsImporter;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:ItunesLibraryImportPlaylists
class ItunesLibraryImportPlaylistsCommand extends Command
{
    protected $signature = 'command:ItunesLibraryImportPlaylists';

    private string $channel;

    private int $perPage = 50;

    public function handle()
    {
        $this->channel = 'itunes_library_import_playlists';

        $filterValues['page'] ??= 1;
        $filterValues['per_page'] ??= $this->perPage;

        Logger::deleteChannel($this->channel);

        $itunesLibrary = new ItunesLibrary;
        $itunesLibrary->getItunesLibrary();

        $playlistsImporter = new ItunesLibraryPlaylistsImporter($itunesLibrary);
        $playlistsImporter->setPerPage($this->perPage);
        $lastPage = $playlistsImporter->getLastPage();

        if (App::environment() == 'local') {
            // Logger::echo($this->channel);
            Logger::log('info', $this->channel, 'Trying');
        }

        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            ItunesLibraryImportPlaylistsJob::dispatchSync(
                [
                    'page' => $page,
                    'per_page' => $filterValues['per_page'],
                ]
            );
            $this->output->progressAdvance();
        }
        unset($itunesLibrary);

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
