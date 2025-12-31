<?php

namespace App\Jobs\Spotify;

use App\Models\Music\Song;
use App\Services\Spotify\Importers\SpotifyTrackSearchAndImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// php artisan job:dispatch SpotifyTrackImportJob
class SpotifyTrackImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $songId = 0;

    private $response;

    public function __construct(int $songId)
    {
        $this->songId = $songId;
    }

    public function handle()
    {
        $api = (new SpotifyApiConnect)->getApi();

        // Load full song with relation
        $song = Song::with('album.artist')->find($this->songId);

        $spotifyTrackSearchAndImporter = new SpotifyTrackSearchAndImporter($api);
        $spotifyTrackSearchAndImporter->import($song);

        $this->response = $spotifyTrackSearchAndImporter->getResponse();

        return $this->response;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
