<?php

namespace App\Jobs\Spotify;

use App\Services\Spotify\Importers\SpotifyPlaylistsImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch SpotifyPlaylistImportJob
class SpotifyPlaylistImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $page = 1;

    private int $perPage = 0;

    private JsonResponse // $response;

    public function __construct(int $page, int $perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function handle()
    {
        $api = (new SpotifyApiConnect)->getApi();
        $spotifyPlaylistImporter = new SpotifyPlaylistsImporter($api, $this->perPage);
        $spotifyPlaylistImporter->import($this->page);
        $this->response = $spotifyPlaylistImporter->getResponse();

        return $this->response;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
