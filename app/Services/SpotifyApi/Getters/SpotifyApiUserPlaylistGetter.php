<?php

namespace App\Services\SpotifyApi\Getters;

use App\Helpers\PaginationHelper;

// get spotify user playlists via api (defined in config)
class SpotifyApiUserPlaylistGetter
{
    private $api;

    private $spotifyUserPlaylists;

    private $importedSpotifyUserPlaylists;

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

        $this->importedSpotifyUserPlaylists = $this->api->getUserPlaylists(config('spotify.spotify_user_id'), [
            'limit' => $this->perPage,
            'offset' => $offset,
        ]);

        $this->total = $this->importedSpotifyUserPlaylists->total;
        $this->calculateLastPage();

        $this->spotifyUserPlaylists = [];
        foreach ($this->importedSpotifyUserPlaylists->items as $playlist) {
            $this->spotifyUserPlaylists[] = $playlist;
        }

        return $this->spotifyUserPlaylists;
    }

    public function getByName(string $playlistName)
    {
        $offset = 0;
        $found = null;

        do {
            $playlists = $this->api->getMyPlaylists([
                'limit' => $this->perPage,
                'offset' => $offset,
            ]);

            foreach ($playlists->items as $playlist) {
                if (strtolower($playlist->name) === strtolower($playlistName)) {
                    $found = $playlist;
                    break 2; // Exit both loops
                }
            }

            $offset += $this->perPage;
        } while (count($playlists->items) > 0);

        return $found;
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
