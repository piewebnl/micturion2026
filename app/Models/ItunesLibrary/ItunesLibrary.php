<?php

namespace App\Models\ItunesLibrary;

use App\Models\Setting;
use App\Services\ItunesLibrary\ItunesLibraryParser;
use App\Services\Logger\Logger;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ItunesLibrary extends Model
{
    private $itunesLibrary; // ["Tracks", "Playlists", "TracksIndexed"]

    private $itunesLibraryXmlFile; // filename on storage

    private $itunesLibraryExtraTracksCsvFile;

    private $useCaching; // set to false when testing

    private $dateDb; // the date stored in DB after last import

    private $dateXml; // the date stored in the xml file

    private $dateDbExtraTracks;

    private $dateCsvExtraTracks;

    public function __construct(bool $useCaching = true)
    {
        $this->useCaching = $useCaching;
        $this->itunesLibraryXmlFile = storage_path(config('ituneslibrary.itunes_library_xml_file'));
        $this->itunesLibraryExtraTracksCsvFile = ltrim(config('ituneslibrary.itunes_library_extra_tracks_csv_file'), '/');
    }

    public function getItunesLibrary()
    {
        if (!$this->itunesLibrary) {
            $library = $this->loadItunesLibrary();
            $this->setItunesLibrary($library);
        }

        return $this->itunesLibrary;
    }

    public function setItunesLibrary($itunesLibrary)
    {
        $this->itunesLibrary = $itunesLibrary;
    }

    // Load the total parsed itunesLibrary
    public function loadItunesLibrary()
    {

        // Try getting the already parsed itunesLibrary from cache
        if ($this->itunesLibrary == null && $this->useCaching == true) {
            $this->itunesLibrary['Tracks'] = Cache::get($this->itunesLibraryXmlFile . '-tracks');
            $this->itunesLibrary['Playlists'] = Cache::get($this->itunesLibraryXmlFile . '-playlists');
        }
        // If not found, get from storage and parse
        if ($this->itunesLibrary == null) {

            Logger::log('info', 'itunes_library_importer', 'iTunes library loaded from file: ' . $this->filename);

            $itunesLibraryParser = new ItunesLibraryParser;
            $this->itunesLibrary = $itunesLibraryParser->parse($thid->itunesLibraryXmlFile);

            Cache::put($this->itunesLibraryXmlFile . '-tracks', $this->itunesLibrary['Tracks']);
            Cache::put($this->itunesLibraryXmlFile . '-playlists', $this->itunesLibrary['Playlists']);
            Logger::log('info', 'itunes_library_importer', 'iTunes library parsed and put in Cache');
        }

        $this->getDateXml();
        $this->getDateDb();
        $this->getDateCsvExtraTracks();
        $this->getDateDbExtraTracks();

        return $this->itunesLibrary;
    }

    public function getTotalTracks()
    {
        return count((array) $this->getTracks());
    }

    public function getTracksModifedAfterDate($date)
    {

        $new = new \stdClass;
        $counter = 0;

        foreach ($this->itunesLibrary['Tracks'] as $track) {
            if ($track->{'Date Modified'} >= $date) {
                $new->{$counter} = $track;
                $counter++;
            }
        }

        return $new;
    }

    public function getTracks(): array
    {
        return $this->itunesLibrary['Tracks'];
    }

    public function getDateDb()
    {
        if (!$this->dateDb) {
            $this->dateDb = Setting::getSetting('itunes_library_xml_date');
        }

        return $this->dateDb;
    }

    public function getDateDbExtraTracks()
    {

        if (!$this->dateDbExtraTracks) {
            $this->dateDbExtraTracks = Setting::getSetting('itunes_library_csv_extra_tracks_date');
        }

        return $this->dateDbExtraTracks;
    }

    public function getDateCsvExtraTracks()
    {
        if (!$this->dateDbExtraTracks) {
            $defaultFilename = $this->itunesLibraryExtraTracksCsvFile;
            $lastmodified = File::lastModified($this->itunesLibraryExtraTracksCsvFile);
            $lastmodified = DateTime::createFromFormat('U', $lastmodified);
            $lastmodified = $lastmodified->format('Y-m-d H:i:s');

            $this->dateCsvExtraTracks = $lastmodified;
        }

        return $this->dateCsvExtraTracks;
    }

    public function addCsvDateExtraTracksSettings()
    {
        // No date?
        if (!$this->dateCsvExtraTracks) {
            $this->getDateCsvExtraTracks();
        }
        Setting::addSetting('itunes_library_csv_extra_tracks_date', $this->dateCsvExtraTracks);
    }

    public function addXmlDateSettings()
    {

        // No date?
        if (!$this->dateXml) {
            $this->getDateXml();
        }
        Setting::addSetting('itunes_library_xml_date', $this->dateXml);
    }

    public function getDateXml()
    {
        if (!$this->dateXml) {
            // Always parse the xml version again
            $itunesLibraryParser = new ItunesLibraryParser;
            $this->itunesLibrary = $itunesLibraryParser->parse($this->itunesLibraryXmlFile);

            $this->itunesLibrary['Date'] = str_replace('T', ' ', $this->itunesLibrary['Date']);
            $this->itunesLibrary['Date'] = str_replace('Z', '', $this->itunesLibrary['Date']);
            $this->dateXml = $this->itunesLibrary['Date'];
        }

        return $this->dateXml;
    }

    public function hasChanged()
    {

        if (!$this->dateXml) {
            $this->getDateXml();
        }

        if (!$this->dateDb) {
            $this->getDateDb();
        }

        if (!$this->dateDbExtraTracks) {
            $this->getDateDbExtraTracks();
        }

        if (!$this->dateCsvExtraTracks) {
            $this->getDateCsvExtraTracks();
        }

        if ($this->dateXml != $this->dateDb || $this->dateDbExtraTracks != !$this->dateCsvExtraTracks) {
            // echo $this->dateXml . ' vs ' . $this->dateDb . "\r\n";
            // echo $this->dateDbExtraTracks . ' vs ' . !$this->dateCsvExtraTracks . "\r\n";
            return true;
        }
    }

    public function getTrackIds(): array
    {
        $ids = [];

        if ($this->itunesLibrary['Tracks']) {
            foreach ($this->itunesLibrary['Tracks'] as $track) {
                $ids[] = $track->{'Track ID'};
            }

            return $ids;
        }
    }

    public function getPlaylists(): array
    {
        return $this->itunesLibrary['Playlists'];
    }
}
