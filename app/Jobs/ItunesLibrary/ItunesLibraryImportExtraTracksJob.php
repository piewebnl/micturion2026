<?php

namespace App\Jobs\ItunesLibrary;

use App\Services\ItunesLibrary\ItunesLibraryExtraTracksImporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Session;

// php artisan job:dispatch ItunesLibraryImportExtraTracksJob
class ItunesLibraryImportExtraTracksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filterValues = [];

    private $response;

    public function handle()
    {
        $filename = config('ituneslibrary.itunes_library_extra_tracks_csv_file');

        $importer = new ItunesLibraryExtraTracksImporter($filename);
        $importer->import();

        $response = $this->response = $importer->getResponse()->getData();

        $persistantIds['imported'] = (array) Session::get('persistent_ids_imported');
        $persistantIds['not_imported'] = (array) Session::get('persistent_ids_not_imported');

        foreach ($response->resource->tracks as $track) {
            if ($track->persistent_id) {
                if ($track->status == 'success') {
                    $persistantIds['imported'][] = $track->persistent_id;
                } else {
                    $persistantIds['not_imported'][] = $track->persistent_id;
                }
            }
        }

        Session::put('persistent_ids_imported', $persistantIds['imported']);
        Session::put('persistent_ids_not_imported', $persistantIds['not_imported']);

        return $importer->getResponse();
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
