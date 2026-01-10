<?php

namespace App\Services\Spotify\Deleters;

use App\Helpers\PaginationHelper;
use Illuminate\Http\JsonResponse;

// Delete tracks from User Library
class SpotifyTracksFavouriteDeleter
{
    private $api;

    private $perPage = 50;

    private $page = 1;

    private $lastPage;

    private $total = 0;

    private $allSpotifyTrackIds;

    private $response;

    public function __construct($api, array $allSpotifyTrackIds)
    {
        $this->api = $api;
        $this->allSpotifyTrackIds = $allSpotifyTrackIds;
        $this->total = count($this->allSpotifyTrackIds);
        $this->calculateLastPage();
    }

    public function delete(int $page)
    {
        $this->page = $page;

        $tracksToDelete = PaginationHelper::slicePerPage($this->allSpotifyTrackIds, $this->page, $this->perPage);

        if (!$tracksToDelete) {
            return;
        }

        $this->api->deleteMyTracks($tracksToDelete);

        // return response
        // $this->response = response()->success('Spotify tracks deleted');
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
