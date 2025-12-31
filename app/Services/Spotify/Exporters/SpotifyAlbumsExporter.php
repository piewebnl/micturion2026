<?php

namespace App\Services\Spotify\Exporters;

use App\Helpers\PaginationHelper;
use App\Models\Music\Album;
use App\Services\SpotifyApi\Posters\SpotifyApiUserAlbumsPoster;
use Illuminate\Http\JsonResponse;

// Export albums to spotify
class SpotifyAlbumsExporter
{
    private $api;

    private // $response;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $albumsPerPage = [];

    private $albums = [];

    private $spotifyAlbumIds = [];

    private $spotifyAlbumIdsToExport = [];

    private $spotifyAlbumIdsExists = [];

    private $total;

    private Album $album;

    private $resource;

    public function __construct($api, int $perPage)
    {
        $this->api = $api;
        $this->perPage = $perPage;
        $this->album = new Album;
    }

    public function export(int $page)
    {
        $this->page = $page;

        $this->getAlbumPerPage();
        $this->calculateLastPage();

        foreach ($this->albumsPerPage as $album) {
            $this->spotifyAlbumIds[] = $album->spotify_api_album_id;
        }

        if (count($this->spotifyAlbumIds) == 0) {
            $this->response = response()->error('No Spotify Favourite Albums to export');

            return;
        }

        $this->determineAlbumsToExport();

        if ($this->spotifyAlbumIdsToExport) {
            $spotifyAlbumPoster = new SpotifyApiUserAlbumsPoster($this->api);
            $spotifyAlbumPoster->post($this->spotifyAlbumIdsToExport);

            $this->resource = [
                'album_ids' => $this->spotifyAlbumIdsToExport,
                'total' => $this->total,
            ];
            $this->response = response()->success('Spotify albums exported', $this->resource);
        }

        $this->resource = [
            'album_ids' => $this->spotifyAlbumIdsExists,
            'total' => $this->total,
        ];
        $this->response = response()->success('Spotify albums already exist', $this->resource);
    }

    // Only export if not exists
    private function determineAlbumsToExport()
    {

        $whichAlbumIdsExistOnSpotify = $this->api->myAlbumsContains($this->spotifyAlbumIds);

        foreach ($this->albumsPerPage as $key => $album) {
            if (!$whichAlbumIdsExistOnSpotify[$key]) {
                $this->spotifyAlbumIdsToExport[] = $album->spotify_api_album_id;
            } else {
                $this->spotifyAlbumIdsExists[] = $album->spotify_api_album_id;
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

        $this->total = $this->albumsPerPage->total();
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

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
