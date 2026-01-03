<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Models\ItunesLibrary\ItunesLibrary;
use App\Services\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;

// php artisan command:ItunesLibraryImport
class ItunesLibraryImportCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ItunesLibraryImport';

    private string $channel = 'itunes_library_importer';

    public function handle()
    {

        $itunesLibrary = new ItunesLibrary;

        Logger::deleteChannel($this->channel);

        Logger::echoChannel($this->channel);

        if (!$itunesLibrary->hasChanged()) {
            Logger::log('info', $this->channel, 'iTunes Library has not changed: DB: ' . $itunesLibrary->getDateDb() . ' and XML: ' . $itunesLibrary->getDateXml());
            // Logger::echo($this->channel);

            return;
        }
        unset($itunesLibrary);

        // $this->clearCache('music');

        $this->call('command:ItunesLibraryImportTracks');
        $this->call('command:ItunesLibraryImportPlaylists');
        $this->call('command:ItunesLibraryImportPlaylistTracks');
        $this->call('command:ItunesLibraryImportTracksFavourite');

        $itunesLibrary = new ItunesLibrary;
        $itunesLibrary->addCsvDateExtraTracksSettings();
        $itunesLibrary->addXmlDateSettings();

        // Logger::echo($this->channel);
    }
}
