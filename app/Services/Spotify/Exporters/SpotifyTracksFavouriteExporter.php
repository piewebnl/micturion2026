<?php

namespace App\Services\Spotify\Exporters;

use App\Helpers\PaginationHelper;
use App\Models\Music\Song;
use App\Services\SpotifyApi\Posters\SpotifyApiUserTracksPoster;
use Illuminate\Http\JsonResponse;

// Export songs (favourite songs) to spotify
class SpotifyTracksFavouriteExporter
{
    private $api;

    private // $response;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $tracksFavouritePerPage = [];

    private $tracksFavourite = [];

    private $total;

    private Song $song;

    private $trackFavouriteIdsPerPage = [];

    private $resource;

    public function __construct($api, int $perPage)
    {
        $this->api = $api;
        $this->perPage = $perPage;
        $this->song = new Song;
    }

    public function export(int $page)
    {
        $this->page = $page;

        $this->getTracksFavouritePerPage();
        $this->calculateLastPage();

        foreach ($this->tracksFavouritePerPage as $trackFavourite) {
            $this->trackFavouriteIdsPerPage[] = $trackFavourite->spotify_api_track_id;
        }

        if (!$this->trackFavouriteIdsPerPage) {

            $spotifyTracksPoster = new SpotifyApiUserTracksPoster($this->api);
            $spotifyTracksPoster->post($this->trackFavouriteIdsPerPage);

            $this->resource = [
                'tracks_favourite_ids' => $this->trackFavouriteIdsPerPage,
                'total' => $this->total,
            ];

            $this->response = response()->success('Spotify Favourite Songs exported', $this->resource);
        } else {
            $this->response = response()->error('Spotify Favourite Songs no track Ids');
        }
    }

    private function calculateLastPage()
    {
        $this->lastPage = PaginationHelper::calculateLastPage($this->total, $this->perPage);
    }

    public function getLastPage(): ?int
    {
        $this->getTracksFavourite();
        $this->calculateLastPage();

        return $this->lastPage;
    }

    private function getTracksFavouritePerPage()
    {
        $this->tracksFavouritePerPage = $this->song->getSongsWithSpotifyTrack(
            [
                'page' => $this->page,
                'per_page' => $this->perPage,
                'favourite' => true,
            ]
        );

        $this->total = $this->tracksFavouritePerPage->total();
    }

    public function getTracksFavourite()
    {
        $this->tracksFavourite = $this->song->getSongsWithSpotifyTrack(
            [
                'favourite' => true,
            ]
        );
        $this->total = count($this->tracksFavourite);

        return $this->tracksFavourite;
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
