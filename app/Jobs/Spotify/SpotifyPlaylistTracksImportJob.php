<?php

namespace App\Jobs\Spotify;

use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Spotify\Importers\SpotifyPlaylistTracksImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch SpotifyPlaylistTracksImportJob
class SpotifyPlaylistTracksImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $page = 1;

    private int $perPage = 0;

    private SpotifyPlaylist $spotifyPlaylist;

    private JsonResponse // $response;

    public function __construct(SpotifyPlaylist $spotifyPlaylist, int $page, int $perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->spotifyPlaylist = $spotifyPlaylist;
    }

    public function handle()
    {
        $api = (new SpotifyApiConnect)->getApi();

        $spotifyPlaylistImporter = new SpotifyPlaylistTracksImporter($api, $this->spotifyPlaylist, $this->perPage);
        $spotifyPlaylistImporter->import($this->page);

        $this->response = $spotifyPlaylistImporter->getResponse();

        return $this->response;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
