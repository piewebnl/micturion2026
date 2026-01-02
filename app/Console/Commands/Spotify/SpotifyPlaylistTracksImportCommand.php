<?php

namespace App\Console\Commands\Spotify;

use App\Jobs\Spotify\SpotifyPlaylistTracksImportJob;
use App\Models\Spotify\SpotifyPlaylist;
use App\Models\Spotify\SpotifyPlaylistTrack;
use App\Services\Spotify\Importers\SpotifyPlaylistTracksImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

// php artisan command:SpotifyPlaylistTracksImport
class SpotifyPlaylistTracksImportCommand extends Command
{
    protected $signature = 'command:SpotifyPlaylistTracksImport';

    private string $channel;

    private int $perPage = 50;

    public function handle()
    {
        $this->channel = 'spotify_playlist_tracks_import';

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

        if (!(new SpotifyPlaylist)->areSpotifyPlaylistsImported()) {
            Logger::log('warning', $this->channel, 'No spotify playlists imported yet.');
            // Logger::echo($this->channel);

            return;
        }

        $api = (new SpotifyApiConnect)->getApi();
        if (!$api) {
            // Logger::echo($this->channel);

            return;
        }

        $playlistsToImport = config('spotify.playlist_tracks_to_import_from_spotify');

        $spotifyPlaylists = SpotifyPlaylist::where(function ($query) use ($playlistsToImport) {
            foreach ($playlistsToImport as $name) {
                $query->orWhere('name', 'like', '%' . $name . '%');
            }
        })->get();

        $this->output->progressStart(count($spotifyPlaylists));

        foreach ($spotifyPlaylists as $spotifyPlaylist) {

            // Skip if the playlist snapshot id hasn't changed
            if ($spotifyPlaylist->snapshot_id_has_changed) {

                $spotifyPlaylistImporter = new SpotifyPlaylistTracksImporter($api, $spotifyPlaylist, $this->perPage);
                $lastPage = $spotifyPlaylistImporter->getLastPage();
                $total = $spotifyPlaylistImporter->getTotal();

                for ($page = 1; $page <= $lastPage; $page++) {
                    SpotifyPlaylistTracksImportJob::dispatchSync(
                        $spotifyPlaylist,
                        $page,
                        $this->perPage
                    );
                }

                // Cleanup
                (new SpotifyPlaylistTrack)->deleteNotChanged($spotifyPlaylist);
                Logger::log('info', $this->channel, 'Spotify playlist tracks imported: ' . $spotifyPlaylist->name . ' [' . $total . ' tracks]');
            } else {
                Logger::log('info', $this->channel, 'Spotify playlists tracks (unchanged) ' . $spotifyPlaylist->name);
            }

            $this->output->progressAdvance();
        }

        Cache::flush();

        $this->output->progressFinish();
        // Logger::echo($this->channel);
    }
}
