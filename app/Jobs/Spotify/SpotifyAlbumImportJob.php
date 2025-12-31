<?php

namespace App\Jobs\Spotify;

use App\Models\Music\Album;
use App\Services\Spotify\Importers\SpotifyAlbumSearchAndImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch SpotifyAlbumImportJob
class SpotifyAlbumImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $albumId = 0;

    private // $response;

    public function __construct(int $albumId)
    {
        $this->albumId = $albumId;
    }

    public function handle()
    {
        $api = (new SpotifyApiConnect)->getApi();

        // Load full album with relation
        $album = Album::with('artist')->find($this->albumId);

        $spotifyAlbumSearchAndImporter = new SpotifyAlbumSearchAndImporter($api);
        $spotifyAlbumSearchAndImporter->import($album);

        $this->response = $spotifyAlbumSearchAndImporter->getResponse();

        return $this->response;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
