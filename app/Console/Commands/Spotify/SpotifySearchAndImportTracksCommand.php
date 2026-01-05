<?php

namespace App\Console\Commands\Spotify;

use App\Jobs\Spotify\SpotifyTrackImportJob;
use App\Models\Music\Category;
use App\Models\Music\Song;
use App\Services\Logger\Logger;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:SpotifySearchAndImportTracks
class SpotifySearchAndImportTracksCommand extends Command
{
    protected $signature = 'command:SpotifySearchAndImportTracks';

    private string $channel;

    private $perPage = 50;

    private $songs = [];

    public function handle()
    {
        $this->channel = 'spotify_search_and_import_tracks';

        Logger::deleteChannel($this->channel);

        $api = (new SpotifyApiConnect)->getApi();
        if (!$api) {
            Logger::log('error', $this->channel, 'No valid spotify API connection');
            // Logger::echo($this->channel);

            return;
        }

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }

        $category = new Category;
        $catIds = $category->getCategoriesByName(['Albums', 'EPs']);

        // Local do all, on production limit to 50
        if (App::environment() == 'local') {
            $this->songs = (new Song)->getSongsWithoutSpotifyTrack([
                'page' => 1,
                'per_page' => $this->perPage,
                'categories' => $catIds,
            ]);
            $this->output->progressStart($this->songs->total());
        } else {
            $this->songs = (new Song)->getSongsWithoutSpotifyTrack(
                [
                    'limit' => $this->perPage,
                    'categories' => $catIds,
                ]
            );
            $this->output->progressStart(count($this->songs));
        }

        foreach ($this->songs as $index => $song) {
            SpotifyTrackImportJob::dispatchSync(
                $song->id
            );
            if ($index == $this->perPage) {
                sleep(30);
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
