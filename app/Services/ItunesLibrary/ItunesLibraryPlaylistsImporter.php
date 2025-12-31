<?php

namespace App\Services\ItunesLibrary;

use App\Helpers\PaginationHelper;
use App\Models\ItunesLibrary\ItunesLibrary;
use App\Models\ItunesLibrary\ItunesLibraryPlaylist;
use App\Traits\Logger\Logger;
use Illuminate\Http\JsonResponse;

// Imports itunes library playlists (no tracks) to db
class ItunesLibraryPlaylistsImporter
{
    private $playlists = []; // playlists to import (selected per page)

    private $itunesLibraryPlaylists; // all found iTunes Library Playlists

    private $itunesLibrary;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $totalPlaylists;

    private // $response;

    private $resource = [];

    public function __construct(ItunesLibrary $itunesLibrary)
    {
        $this->itunesLibrary = $itunesLibrary;
    }

    private function init()
    {
        $config = config('ituneslibrary');
        $configValues = $config['itunes_playlists_to_import'];

        $this->itunesLibrary->getItunesLibrary();
        $this->itunesLibraryPlaylists = $this->itunesLibrary->getPlaylists();

        $itunesLibraryPlaylistsParser = new ItunesLibraryPlaylistsParser($this->itunesLibrary);
        $this->itunesLibraryPlaylists = $itunesLibraryPlaylistsParser->getPlaylistsByNames($configValues);

        $this->totalPlaylists = count($this->itunesLibraryPlaylists);

        $this->getItunesLibraryPlaylistsPerPage();

        $this->calculateLastPage();
    }

    public function import(int $page, int $perPage)
    {
        $this->setPage($page);
        $this->setPerPage($perPage);
        $this->init();

        $itunesLibraryPlaylist = new ItunesLibraryPlaylist;
        $itunesLibraryPlaylist->storeAll($this->playlists);

        $this->resource = [
            'page' => $this->page,
            'total_playlists' => $this->totalPlaylists,
            'total_playlists_imported' => count($this->playlists),
            'playlists' => $itunesLibraryPlaylist->getResource(),

        ];

        if ($this->page == $this->lastPage) {
            Logger::log('info', 'itunes_library_import_playlists', 'iTunes library playlists imported: ' . $this->totalPlaylists);
        }

        $this->response = response()->success('Playlists imported', $this->resource);
    }

    private function getItunesLibraryPlaylistsPerPage()
    {
        $this->playlists = PaginationHelper::slicePerPage($this->itunesLibraryPlaylists, $this->page, $this->perPage);
    }

    private function calculateLastPage()
    {
        if ($this->perPage > 0) {
            $this->lastPage = intval(ceil($this->totalPlaylists / $this->perPage));

            return;
        }
        $this->lastPage = 1;
    }

    public function getPlaylists(): ?array
    {
        return $this->playlists;
    }

    public function getLastPage(): ?int
    {

        if (!$this->lastPage) {
            $this->init();
        }

        return $this->lastPage;
    }

    public function getTotalPlaylists(): ?int
    {
        return $this->totalPlaylists;
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

    public function getResponse(): JsonResponse
    {

        return $this->response;
    }
}
