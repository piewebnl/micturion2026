<?php

namespace App\Services\Spotify\Exporters;

use App\Models\Music\Album;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use App\Helpers\PaginationHelper;
use App\Services\SpotifyApi\Posters\SpotifyApiUserAlbumsPoster;

// Export albums to spotify
class SpotifyAlbumsExporter
{
    private $api;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $albumsPerPage = [];

    private $albums = [];

    private $spotifyAlbumIds = [];

    private $spotifyAlbumIdsToExport = [];


    private $total;

    private Album $album;

    private $command;

    private string $channel = 'spotify_albums_export';


    public function __construct($api, int $perPage, Command $command)
    {
        $this->api = $api;
        $this->perPage = $perPage;
        $this->album = new Album;
        $this->command = $command;
    }

    public function export(int $page): array
    {
        $this->page = $page;
        $this->spotifyAlbumIds = [];
        $this->spotifyAlbumIdsToExport = [];

        $this->getAlbumPerPage();
        $this->calculateLastPage();

        foreach ($this->albumsPerPage as $album) {
            $this->spotifyAlbumIds[] = $album->spotify_api_album_id;
        }

        if (count($this->spotifyAlbumIds) == 0) {
            Logger::log('error', $this->channel, 'No valid spotify API connection', [], $this->command);
            return [];
        }

        $this->determineAlbumsToExport();

        if (!empty($this->spotifyAlbumIdsToExport)) {
            $spotifyAlbumPoster = new SpotifyApiUserAlbumsPoster($this->api);
            $done = $spotifyAlbumPoster->post($this->spotifyAlbumIdsToExport);
            $imported = $done ? $this->spotifyAlbumIdsToExport : [];
            Logger::log('notify', $this->channel, 'Added');
            return $imported;
        } else {
            Logger::log('info', $this->channel, 'exists');
        }

        return [];
    }

    // Only export if not exists
    private function determineAlbumsToExport()
    {
        $whichAlbumIdsExistOnSpotify = $this->api->myAlbumsContains($this->spotifyAlbumIds);
        foreach ($this->albumsPerPage as $key => $album) {
            if (!$whichAlbumIdsExistOnSpotify[$key]) {
                $this->spotifyAlbumIdsToExport[] = $album->spotify_api_album_id;
            }
        }
    }

    private function calculateLastPage()
    {
        $this->lastPage = PaginationHelper::calculateLastPage($this->total, $this->perPage);
    }

    public function getLastPage(): ?int
    {
        $this->getAlbums();
        $this->calculateLastPage();

        return $this->lastPage;
    }

    private function getAlbumPerPage()
    {
        $this->albumsPerPage = $this->album->getAlbumsWithSpotifyAlbum(
            [
                'page' => $this->page,
                'per_page' => $this->perPage,
            ]
        );

        if (is_object($this->albumsPerPage) && method_exists($this->albumsPerPage, 'total')) {
            $this->total = $this->albumsPerPage->total();
        } else {
            $this->total = count($this->albumsPerPage);
        }
    }

    public function getAlbums()
    {
        $this->albums = $this->album->getAlbumsWithSpotifyAlbum([]);
        $this->total = count($this->albums);

        return $this->albums;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }
}
