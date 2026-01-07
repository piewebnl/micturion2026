<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Jobs\ItunesLibrary\ItunesLibraryCheckerJob;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:ItunesLibraryChecker
class ItunesLibraryCheckerCommand extends Command
{
    protected $signature = 'command:ItunesLibraryChecker';

    private string $channel;

    public function handle()
    {
        if (App::environment() != 'local') {
            return;
        }

        $this->channel = 'itunes_library_checker';

        Logger::deleteChannel($this->channel);

        $itunesLibraryChecker = new ItunesLibraryChecker;
        // $itunesLibraryChecker->checkEmptyFolders();
        // $itunesLibraryChecker->checkFolderImages();
        $itunesLibraryChecker->checkForLostFiles();
        // $itunesLibraryChecker->checkSongs();
        // $itunesLibraryChecker->checkForMix();
        // $itunesLibraryChecker->checkDiscSet();
        // $itunesLibraryChecker->checkTrackNumbers();

        // Logger::echo($this->channel);
    }
}
