<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Models\ItunesLibrary\ItunesLibrary;
use App\Models\Music\Album;
use App\Models\Music\Artist;
use App\Models\Music\Song;
use App\Services\ItunesLibrary\ItunesLibraryExtraTracksImporter;
use App\Services\ItunesLibrary\ItunesLibraryTracksImporter;
use App\Services\Music\AlbumCalculatePlayCount;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Session;

// php artisan command:ItunesLibraryImportTracks
class ItunesLibraryImportTracksCommand extends Command
{
    protected $signature = 'command:ItunesLibraryImportTracks';

    private string $channel = 'itunes_library_import_tracks';

    private int $perPage = 500;

    private $resource;

    public function handle()
    {

        // Import Extra Tracks first
        $filename = config('ituneslibrary.itunes_library_extra_tracks_csv_file');
        $importer = new ItunesLibraryExtraTracksImporter($filename);
        $importer->import();

        // Import library tracks
        $filterValues['page'] ??= 1;
        $filterValues['per_page'] ??= $this->perPage;

        $itunesLibraryXmlFile = storage_path(config('ituneslibrary.itunes_library_xml_file'));

        $itunesLibrary = new ItunesLibrary($itunesLibraryXmlFile);
        $importer = new ItunesLibraryTracksImporter($itunesLibrary);
        $importer->setPerPage($this->perPage);
        $lastPage = $importer->getLastPage();

        $this->output->progressStart($lastPage);

        Logger::deleteChannel($this->channel);
        Logger::log('info', $this->channel, 'iTunes library changed: ' . date('d-m-Y h:i', strtotime($itunesLibrary->getDateXml())) . ' in xml');

        for ($page = 1; $page <= $lastPage; $page++) {
            $importer->import($page, $this->perPage);
            $this->resource = $importer->getResource();

            $persistantIds['imported'] = (array) Session::get('persistent_ids_imported');
            $persistantIds['not_imported'] = (array) Session::get('persistent_ids_not_imported');

            foreach ($this->resource['tracks'] as $track) {

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

            $this->output->progressAdvance();
        }
        unset($itunesLibrary);

        $notImportedIds = Session::get('persistent_ids_not_imported');

        foreach ($notImportedIds as $id) {
            Song::where('persistent_id', $id)->delete();
        }

        // delete all old stuff
        $allPersistentIds = Song::pluck('persistent_id')->toArray();

        $importedIds = Session::get('persistent_ids_imported');
        $diff = array_diff($allPersistentIds, $importedIds);

        Song::whereIn('persistent_id', $diff)->delete();

        // Delete albums without songs
        Album::doesntHave('songs')->delete();

        // Delete artists without albums
        Artist::doesntHave('songs')->delete();

        // Album count
        $albumCalculatePlayCount = new AlbumCalculatePlayCount;
        $albumCalculatePlayCount->calculate();

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
