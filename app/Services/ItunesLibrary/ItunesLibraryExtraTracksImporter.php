<?php

namespace App\Services\ItunesLibrary;

use App\Traits\Logger\Logger;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use App\Models\ItunesLibrary\ItunesLibraryTrack;

// Imports extra itunes library tracks from a text file to the db
class ItunesLibraryExtraTracksImporter
{
    private $extraTracks;

    private $itunesLibraryExtraTtracksCsv;

    private $date;

    private $channel = 'itunes_library_import_extra_tracks';

    public function __construct(string $itunesLibraryExtraTtracksCsv)
    {
        $this->itunesLibraryExtraTtracksCsv = ltrim($itunesLibraryExtraTtracksCsv, '/');
        $this->loadExtraTracks();
    }

    public function import(): void
    {
        $itunesLibraryTrack = new ItunesLibraryTrack;

        if (!$this->extraTracks) {
            Logger::log('error', $this->channel, 'No extra tracks found');
            return;
        }

        $persistantIds['imported'] = (array) Session::get('persistent_ids_imported');
        $persistantIds['not_imported'] = (array) Session::get('persistent_ids_not_imported');


        $itunesLibraryTracks = [];
        foreach ($this->extraTracks as $track) {

            $itunesLibraryTrack->storeTrack((array) $track);
            $itunesLibraryTracks = $itunesLibraryTrack->getResource();
        }

        foreach ($itunesLibraryTracks as $track) {
            if ($track['persistent_id']) {
                if ($track['status'] == 'success') {
                    $persistantIds['imported'][] = $track['persistent_id'];
                } else {
                    $persistantIds['not_imported'][] = $track['persistent_id'];
                }
            }
        }

        Session::put('persistent_ids_imported', $persistantIds['imported']);
        Session::put('persistent_ids_not_imported', $persistantIds['not_imported']);
    }

    public function loadExtraTracks(): void
    {
        try {

            $file = fopen($this->itunesLibraryExtraTtracksCsv, 'r');
            $this->extraTracks = fread($file, filesize($this->itunesLibraryExtraTtracksCsv));
            $this->date = date('Y-m-d H:i:s', filemtime($this->itunesLibraryExtraTtracksCsv));

            $this->convertExtraTracks();
        } catch (\Throwable $e) {
            Logger::log('error', $this->channel, 'Not found: ' . $this->itunesLibraryExtraTtracksCsv);
        }
    }

    public function getextraTracks(): array
    {
        return $this->extraTracks;
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
