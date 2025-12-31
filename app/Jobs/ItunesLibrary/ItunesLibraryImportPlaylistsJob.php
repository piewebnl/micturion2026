<?php

namespace App\Jobs\ItunesLibrary;

use App\Models\ItunesLibrary\ItunesLibrary;
use App\Services\ItunesLibrary\ItunesLibraryPlaylistsImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch ItunesLibraryImportPlaylistsJob
class ItunesLibraryImportPlaylistsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    private $filterValues = [];

    private // $response;

    public function __construct($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    public function handle()
    {

        $itunesLibrary = new ItunesLibrary;
        $importer = new ItunesLibraryPlaylistsImporter($itunesLibrary);
        $importer->import($this->filterValues['page'], $this->filterValues['per_page']);

        $this->response = $importer->getResponse();
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
