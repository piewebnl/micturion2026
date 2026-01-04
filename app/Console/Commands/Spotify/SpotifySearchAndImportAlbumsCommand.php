<?php

namespace App\Console\Commands\Spotify;

use App\Models\Music\Album;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\Jobs\Spotify\SpotifyAlbumImportJob;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\Spotify\Importers\SpotifyPlaylistsImporter;
use App\Services\Spotify\Importers\SpotifyAlbumSearchAndImporter;

// php artisan command:SpotifySearchAndImportAlbums
class SpotifySearchAndImportAlbumsCommand extends Command
{
    protected $signature = 'command:SpotifySearchAndImportAlbums';

    protected $description = 'Try and match iTunes albums with Spotify albums via api';

    private string $channel = 'spotify_search_and_import_albums';

    private $perPage = 50;


    public function handle()
    {
        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);


        $api = (new SpotifyApiConnect($this))->getApi();

        if (!$api) {
            return self::FAILURE;
        }

        $albums = (new Album)->getAlbumsWithoutSpotifyAlbum(
            [
                'limit' => $this->perPage,
                'categories' => [1],
            ]
        );


        $this->output->progressStart(count($albums));

        foreach ($albums as $album) {
            echo $album->name . "\r\n";
            $spotifyAlbumSearchAndImporter = new SpotifyAlbumSearchAndImporter($api);
            $spotifyAlbumSearchAndImporter->import($album);
            sleep(1);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
