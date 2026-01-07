<?php

namespace App\Console\Commands\Spotify;

use App\Models\Spotify\SpotifyAlbum;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\Spotify\Importers\SpotifyAlbumTracksImporter;

// php artisan command:SpotifySearchAndImportAlbumTracks
class SpotifySearchAndImportAlbumTracksCommand extends Command
{
    protected $signature = 'command:SpotifySearchAndImportAlbumTracks';

    protected $description = 'Import Spotify tracks by album and match to iTunes songs';

    private string $channel = 'spotify_search_and_import_tracks';


    public function handle()
    {

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $api = (new SpotifyApiConnect($this))->getApi();

        if (!$api) {
            return self::FAILURE;
        }


        $spotifyAlbums = SpotifyAlbum::whereNotNull('spotify_api_album_id')
            ->get(['album_id', 'spotify_api_album_id', 'name', 'artwork_url']);
        $this->output->progressStart($spotifyAlbums->count());

        foreach ($spotifyAlbums as $spotifyAlbum) {
            $spotifyAlbumTracksImporter = new SpotifyAlbumTracksImporter($api);
            $spotifyAlbumTracksImporter->import($spotifyAlbum);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
