<?php

namespace App\Services\SpotifyApi\Getters;

use App\Helpers\PaginationHelper;
use App\Models\Spotify\SpotifyPlaylist;

// get spotify playlist tracks via api
class SpotifyApiUserPlaylistTracksGetter
{
    private $api;

    private $spotifyApiPlaylistTracks;

    private $spotifyPlaylist;

    private $page = 1;

    private $perPage = 50;

    private $lastPage = null;

    private $total = 0;

    public function __construct($api, SpotifyPlaylist $spotifyPlaylist, int $perPage)
    {
        $this->api = $api;
        $this->spotifyPlaylist = $spotifyPlaylist;
        $this->perPage = $perPage;
    }

    public function getPerPage(int $page): array
    {
        $this->page = $page;

        $offset = ($this->page - 1) * $this->perPage;

        $this->spotifyApiPlaylistTracks = [];

        $tracks = $this->api->getPlaylistTracks($this->spotifyPlaylist->spotify_api_playlist_id, [
            'limit' => $this->perPage,
            'offset' => $offset,
        ]);

        $this->total = $tracks->total;
        $this->calculateLastPage();

        foreach ($tracks->items as $track) {
            $this->spotifyApiPlaylistTracks[] = $track->track;
        }

        return $this->spotifyApiPlaylistTracks;
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
