<?php

namespace App\Services\SpotifyApi\Getters;

use App\Helpers\PaginationHelper;

// get spotify tracks (favourite songs from user library)
class SpotifyApiUserFavouriteTracksGetter
{
    private $api;

    private $spotifyUserFavouriteTracksIds = [];

    private $page = 1;

    private $perPage = 50;

    private $lastPage = null;

    private $total = 0;

    public function __construct($api, int $perPage)
    {
        $this->api = $api;
        $this->perPage = $perPage;
    }

    public function getPerPage(int $page): array
    {
        $this->page = $page;

        $offset = ($this->page - 1) * $this->perPage;

        $myFavouriteTracks = $this->api->getMySavedTracks([
            'limit' => $this->perPage,
            'offset' => $offset,
        ]);

        $this->total = $myFavouriteTracks->total;

        $this->calculateLastPage();

        $this->spotifyUserFavouriteTracksIds = [];

        foreach ($myFavouriteTracks->items as $track) {
            $this->spotifyUserFavouriteTracksIds[] = $track->track->id;
        }

        return $this->spotifyUserFavouriteTracksIds;
    }

    public function calculateLastPage()
    {
        $this->lastPage = PaginationHelper::calculateLastPage($this->total, $this->perPage);
    }

    public function getLastPage(): ?int
    {
        $this->getPerPage(1);
        $this->calculateLastPage();

        return $this->lastPage;
    }

    public function getTotal(): int
    {
        $this->getPerPage(1);

        return $this->total;
    }
}
