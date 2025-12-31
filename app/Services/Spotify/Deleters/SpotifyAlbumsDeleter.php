<?php

namespace App\Services\Spotify\Deleters;

use App\Helpers\PaginationHelper;
use Illuminate\Http\JsonResponse;

// Delete albums from User Library
class SpotifyAlbumsDeleter
{
    private $api;

    private $perPage = 50;

    private $page = 1;

    private $lastPage;

    private $total = 0;

    private $allSpotifyAlbumIds;

    private // $response;

    public function __construct($api, array $allSpotifyAlbumIds)
    {
        $this->api = $api;
        $this->allSpotifyAlbumIds = $allSpotifyAlbumIds;
        $this->total = count($this->allSpotifyAlbumIds);
        $this->calculateLastPage();
    }

    public function delete(int $page)
    {
        $this->page = $page;

        $albumsToDelete = PaginationHelper::slicePerPage($this->allSpotifyAlbumIds, $this->page, $this->perPage);

        if (!$albumsToDelete) {
            return;
        }

        $this->api->deleteMyAlbums($albumsToDelete);

        // return response
        // $this->response = response()->success('Spotify albums deleted');
    }

    private function getSpotifyAlbumsPerPage()
    {
        /*
        $count = 0;
        $this->start = ($this->page - 1) * $this->perPage;
        $this->end = $this->start + $this->perPage;

        foreach ($this->allSpotifyAlbumIds as $track) {
            if ($count >= $this->start and $count < $this->end) {
                $this->allSpotifyAlbumIdsPage[] = $track;
            }
            $count++;
        }
        */
    }

    private function calculateLastPage()
    {
        $this->lastPage = PaginationHelper::calculateLastPage($this->total, $this->perPage);
    }

    public function getLastPage()
    {
        return $this->lastPage;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
