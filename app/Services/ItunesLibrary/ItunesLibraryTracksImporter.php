<?php

namespace App\Services\ItunesLibrary;

use App\Helpers\PaginationHelper;
use App\Models\ItunesLibrary\ItunesLibrary;
use App\Models\ItunesLibrary\ItunesLibraryTrack;
use App\Models\Setting;
use App\Traits\Logger\Logger;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

// Import iTunes library tracks (songs) to db
class ItunesLibraryTracksImporter
{
    private $tracks; // tracks to import (selected per page)

    private $itunesLibraryTracks; // all iTunes Library Tracks

    private $itunesLibrary;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $totalTracks;

    private $resource;


    public function __construct(ItunesLibrary $itunesLibrary)
    {
        $this->itunesLibrary = $itunesLibrary;
    }

    private function init()
    {
        $this->itunesLibrary->getItunesLibrary();
        $this->itunesLibraryTracks = $this->itunesLibrary->getTracks();
        $this->totalTracks = $this->itunesLibrary->getTotalTracks();

        $this->calculateLastPage();
    }

    public function import(int $page, int $perPage)
    {
        $this->setPage($page);
        $this->setPerPage($perPage);

        $this->init();
        $itunesLibraryTrack = new ItunesLibraryTrack;

        // Get tracks per page or all
        if ($this->perPage > 0) {
            $this->getItunesLibraryTracksPerPage();
        } else {
            $this->getItunesLibraryAllTracks();
        }

        $itunesLibraryTrack->storeTracks($this->tracks);

        $this->resource = [
            'page' => $this->page,
            'total_tracks' => $this->totalTracks,
            'total_tracks_imported' => count($this->tracks),
            'tracks' => $itunesLibraryTrack->getResource(),
        ];

        if ($this->page == $this->lastPage) {
            Logger::log('info', 'itunes_library_import_tracks', 'Tracks importerd: ' . $this->totalTracks);
        }
    }

    private function getItunesLibraryAllTracks()
    {
        $count = 0;

        foreach ($this->itunesLibraryTracks as $track) {
            $this->tracks[] = $track;
            $count++;
        }
    }

    private function getItunesLibraryTracksPerPage()
    {
        $this->tracks = PaginationHelper::slicePerPage($this->itunesLibraryTracks, $this->page, $this->perPage);
    }

    private function calculateLastPage()
    {
        if ($this->perPage > 0) {
            $this->lastPage = (int) ceil($this->totalTracks / $this->perPage);

            return;
        }
        $this->lastPage = 1;
    }

    public function getTracks(): ?array
    {
        return $this->tracks;
    }

    public function getLastPage(): ?int
    {
        if (!$this->lastPage) {
            $this->init();
            $this->calculateLastPage();
        }

        return $this->lastPage;
    }

    public function getTotalTracks(): ?int
    {
        return $this->totalTracks;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(?int $page = 1)
    {

        $this->page = $page;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(?int $perPage)
    {

        $this->perPage = $perPage;

        return $this;
    }

    public function getResource()
    {

        return $this->resource;
    }
}
