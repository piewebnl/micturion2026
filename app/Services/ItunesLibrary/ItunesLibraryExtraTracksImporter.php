<?php

namespace App\Services\ItunesLibrary;

use App\Traits\Logger\Logger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\ItunesLibrary\ItunesLibraryTrack;

// Imports extra itunes library tracks from a text file to the db
class ItunesLibraryExtraTracksImporter
{
    private ?array $extraTracks = [];

    private string $itunesLibraryExtraTracksCsv;

    private string $parsedExtratracks = '';

    private ?string $date = null;

    private string $channel = 'itunes_library_import_extra_tracks';


    public function __construct(string $itunesLibraryExtraTracksCsv)
    {
        $this->itunesLibraryExtraTracksCsv = ltrim($itunesLibraryExtraTracksCsv, '/');
        $this->loadExtraTracks();
    }

    public function import(): void
    {
        if (!$this->extraTracks) {
            Logger::log('error', $this->channel, 'No extra tracks found');
            return;
        }

        $itunesLibraryTrack = new ItunesLibraryTrack;
        $persistantIds = [
            'imported' => (array) Session::get('persistent_ids_imported', []),
            'not_imported' => (array) Session::get('persistent_ids_not_imported', []),
        ];

        $itunesLibraryTracks = [];
        foreach ($this->extraTracks as $track) {
            $itunesLibraryTrack->storeTrack((array) $track);
            $itunesLibraryTracks = array_merge($itunesLibraryTracks, (array) $itunesLibraryTrack->getResource());
        }

        foreach ($itunesLibraryTracks as $track) {
            if (!empty($track['persistent_id'])) {
                if ($track['status'] === 'success') {
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
            $file = fopen($this->itunesLibraryExtraTracksCsv, 'r');
            if ($file === false) {
                throw new \Exception('Cannot open file: ' . $this->itunesLibraryExtraTracksCsv);
            }

            $this->parsedExtratracks = fread($file, filesize($this->itunesLibraryExtraTracksCsv));
            fclose($file);
            $this->date = date('Y-m-d H:i:s', filemtime($this->itunesLibraryExtraTracksCsv));

            $this->convertExtraTracks();
        } catch (\Throwable $e) {
            Logger::log('error', $this->channel, 'Not found: ' . $this->itunesLibraryExtraTracksCsv);
        }
    }

    public function getExtraTracks(): array
    {
        return $this->extraTracks ?? [];
    }

    private function convertExtraTracks(): void
    {
        $tracks = preg_split('/\n|\r\n?/', $this->parsedExtratracks);

        $new = [];
        $headers = [];

        $headerFields = explode("\t", $tracks[0]);
        foreach ($headerFields as $field) {
            $headers[] = $field;
        }

        foreach ($tracks as $key => $track) {
            if ($key > 0 && !empty($track)) {
                $fields = explode("\t", $track);
                $trackData = [];
                foreach ($fields as $headerKey => $field) {
                    if (isset($headers[$headerKey])) {
                        $trackData[$headers[$headerKey]] = $field;
                    }
                }

                $trackData['Is Extra'] = true;
                $hash = substr(md5($trackData['Name'] . $trackData['Sort Album'] . $trackData['Artist']), 0, 16);
                $trackData['Persistent ID'] = $hash;
                $trackData['Persistent Album ID'] = 'EXTRA' . $key;

                $formattedDate = Carbon::parse($this->date)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
                $trackData['Date Added'] = $formattedDate;
                $trackData['Date Modified'] = $formattedDate;

                $new[$key - 1] = $trackData;
            }
        }
        $this->extraTracks = $new;
    }
}
