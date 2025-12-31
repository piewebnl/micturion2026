<?php

namespace App\Services\SpotifyApi\Getters;

use App\Helpers\PaginationHelper;

// get spotify albums
class SpotifyApiUserAlbumsGetter
{
    private $api;

    private $spotifyUserAlbumIds = [];

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

        $myAlbums = $this->api->getMySavedAlbums([
            'limit' => $this->perPage,
            'offset' => $offset,
        ]);

        $this->total = $myAlbums->total;

        $this->calculateLastPage();

        $this->spotifyUserAlbumIds = [];

        foreach ($myAlbums->items as $album) {
            $this->spotifyUserAlbumIds[] = $album->album->id;
        }

        return $this->spotifyUserAlbumIds;
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
