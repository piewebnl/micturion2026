<?php

namespace App\Services\ItunesLibrary;

use App\Models\ItunesLibrary\ItunesLibraryTrack;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

// Imports extra itunes library tracks from a text file to the db
class ItunesLibraryExtraTracksImporter
{
    private $extraTracks;

    private $itunesLibraryExtraTtracksCsv;

    private $response;

    private $date;

    private $resource = [];

    public function __construct(string $itunesLibraryExtraTtracksCsv)
    {
        $this->itunesLibraryExtraTtracksCsv = ltrim($itunesLibraryExtraTtracksCsv, '/');
        $this->loadExtraTracks();
    }

    public function import(): void
    {
        $itunesLibraryTrack = new ItunesLibraryTrack;

        if (!$this->extraTracks) {
            $this->response = response()->error('No extra tracks found');

            return;
        }

        $itunesLibraryTracks = [];
        foreach ($this->extraTracks as $track) {

            $itunesLibraryTrack->storeTrack((array) $track);
            $itunesLibraryTracks = $itunesLibraryTrack->getResource();
        }

        $this->resource = [
            'page' => 1,
            'total_tracks' => count($this->extraTracks),
            'last_page' => 1,
            'total_tracks_imported' => count($this->extraTracks),
            'tracks' => $itunesLibraryTracks,
        ];

        $this->response = response()->success('Extra tracks imported', $this->resource);
    }

    public function loadExtraTracks(): void
    {
        try {

            $file = fopen($this->itunesLibraryExtraTtracksCsv, 'r');
            $this->extraTracks = fread($file, filesize($this->itunesLibraryExtraTtracksCsv));
            $this->date = date('Y-m-d H:i:s', filemtime($this->itunesLibraryExtraTtracksCsv));

            $this->convertExtraTracks();
        } catch (\Throwable $e) {

            $this->response = response()->error('Not found: ' . $this->itunesLibraryExtraTtracksCsv);
        }
    }

    public function getextraTracks(): array
    {
        return $this->extraTracks;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }

    private function convertExtraTracks(): void
    {

        $tracks = preg_split('/\n|\r\n?/', $this->extraTracks);

        $new = null;
        $headers = [];

        // Get headers from the first line
        $headerFields = explode("\t", $tracks[0]);

        foreach ($headerFields as $field) {
            $headers[] = $field;
        }

        foreach ($tracks as $key => $track) {

            if ($key > 0) {

                $fields = explode("\t", $track);
                foreach ($fields as $headerKey => $field) {
                    $new[$key - 1][$headers[$headerKey]] = $field;
                }
                $new[$key - 1]['Is Extra'] = true;
                $hash = substr(md5($new[$key - 1]['Name'] . $new[$key - 1]['Sort Album'] . $new[$key - 1]['Artist']), 0, 16);
                $new[$key - 1]['Persistent ID'] = $hash;
                $hash = substr(md5($new[$key - 1]['Sort Album'] . $new[$key - 1]['Artist']), 0, 16);
                $new[$key - 1]['Persistent Album ID'] = 'EXTRA' . $key;

                // Get date of csv
                $new[$key - 1]['Date Added'] = Carbon::parse($this->date)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
                $new[$key - 1]['Date Modified'] = Carbon::parse($this->date)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

                // echo $new[$key - 1]['Persistent ID'] . "\r\n";
            }
        }
        $this->extraTracks = $new;
    }
}
