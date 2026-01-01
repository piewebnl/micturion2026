<?php

namespace App\Jobs\Spotify;

use App\Services\Spotify\Exporters\SpotifyTracksFavouriteExporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch SpotifyTracksFavouriteExport
class SpotifyTracksFavouriteExportJob
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    private int $page = 1;

    private int $perPage = 0;

    private JsonResponse $response;

    public function __construct(int $page, int $perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function handle()
    {
        $api = (new SpotifyApiConnect)->getApi();

        $spotifyPlaylistTracksExporter = new SpotifyTracksFavouriteExporter($api, $this->perPage);
        $spotifyPlaylistTracksExporter->export($this->page);

        $this->response = $spotifyPlaylistTracksExporter->getResponse();

        return $this->response;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
