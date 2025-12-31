<?php

namespace App\Jobs\ItunesLibrary;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch ItunesLibraryImportPlaylistTracksJob
class ItunesLibraryImportPlaylistTracksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    private $filterValues = [];

    private // $response;

    protected $playlistTracksImporter;

    public function __construct($playlistTracksImporter, $filterValues)
    {
        $this->filterValues = $filterValues;
        $this->playlistTracksImporter = $playlistTracksImporter;
    }

    public function handle()
    {

        if (isset($this->filterValues['import_favourite'])) {
            $this->playlistTracksImporter->setImportFavourite(true);
        }

        $this->playlistTracksImporter->importPerPage($this->filterValues['page']);

        $this->response = $this->playlistTracksImporter->getResponse();
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
