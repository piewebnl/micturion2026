<?php

namespace App\Jobs\ItunesLibrary;

use App\Services\ItunesLibrary\ItunesLibraryChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch ItunesLibraryCheckFilesJob
class ItunesLibraryCheckerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private // $response;

    public function handle()
    {

        $itunesLibraryChecker = new ItunesLibraryChecker;
        // $itunesLibraryChecker->checkEmptyFolders();
        // $itunesLibraryChecker->checkFolderImages();
        $itunesLibraryChecker->checkForLostFiles();
        // $itunesLibraryChecker->checkSongs();
        // $itunesLibraryChecker->checkForMix();
        // $itunesLibraryChecker->checkDiscSet();
        // $itunesLibraryChecker->checkTrackNumbers();
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
