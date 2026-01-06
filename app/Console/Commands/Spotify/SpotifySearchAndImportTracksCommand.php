<?php

namespace App\Console\Commands\Spotify;

use App\Models\Music\Song;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\Spotify\Importers\SpotifyTrackSearchAndImporter;

// php artisan command:SpotifySearchAndImportTracks
class SpotifySearchAndImportTracksCommand extends Command
{
    protected $signature = 'command:SpotifySearchAndImportTracks';

    protected $description = 'Try and match iTunes songs with Spotify songs via api';

    private string $channel = 'spotify_search_and_import_tracks';


    public function handle()
    {

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $api = (new SpotifyApiConnect($this))->getApi();

        if (!$api) {
            return self::FAILURE;
        }

        $songs = (new Song)->getSongsWithoutSpotifyTrack([
            'categories' => [1, 2],
        ]);


        $this->output->progressStart(count($songs));

        foreach ($songs as $song) {
            $spotifyAlbumSearchAndImporter = new SpotifyTrackSearchAndImporter($api);
            $spotifyAlbumSearchAndImporter->import($song);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
