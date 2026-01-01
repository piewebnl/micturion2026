<?php

namespace App\Jobs\Spotify;

use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\SpotifyApi\Getters\SpotifyApiUserFavouriteTracksGetter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch SpotifyTracksFavouriteGetterJob
class SpotifyTracksFavouriteGetterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $response;

    private int $page = 1;

    private int $perPage = 0;

    public function __construct(int $page, int $perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function handle()
    {
        $api = (new SpotifyApiConnect)->getApi();

        $spotifyApiUserFavouriteTracksGetter = new SpotifyApiUserFavouriteTracksGetter($api, $this->perPage);
        $ids = $spotifyApiUserFavouriteTracksGetter->getPerPage($this->page);

        $this->response = response()->success('Spotify Favourite Songs getted', $ids);

        return $this->response;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
