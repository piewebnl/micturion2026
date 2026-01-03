<?php

namespace App\Console\Commands\Spotify;

use App\Jobs\Spotify\SpotifyPlaylistTracksImportJob;
use App\Models\Spotify\SpotifyPlaylist;
use App\Models\Spotify\SpotifyPlaylistTrack;
use App\Services\Spotify\Importers\SpotifyPlaylistTracksImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

// php artisan command:SpotifyPlaylistTracksImport
class SpotifyPlaylistTracksImportCommand extends Command
{
    protected $signature = 'command:SpotifyPlaylistTracksImport';

    private string $channel = 'spotify_playlist_tracks_import';

    private int $perPage = 50;

    public function handle()
    {

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $api = (new SpotifyApiConnect($this))->getApi();

        if (!$api) {
            return self::FAILURE;
        }

        if (!(new SpotifyPlaylist)->areSpotifyPlaylistsImported()) {
            Logger::log('error', $this->channel, 'No spotify playlists imported yet.', [], $this);
            return self::FAILURE;
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
            $test = true;
            if ($spotifyPlaylist->snapshot_id_has_changed or $test = true) {

                $spotifyPlaylistImporter = new SpotifyPlaylistTracksImporter($api, $spotifyPlaylist, $this->perPage);
                $lastPage = $spotifyPlaylistImporter->getLastPage();
                $total = $spotifyPlaylistImporter->getTotal();

                for ($page = 1; $page <= $lastPage; $page++) {
                    $spotifyPlaylistImporter = new SpotifyPlaylistTracksImporter($api, $spotifyPlaylist, $this->perPage);
                    $spotifyPlaylistImporter->import($page);
                }

                // Cleanup
                (new SpotifyPlaylistTrack)->deleteNotChanged($spotifyPlaylist);
                Logger::log('notice', $this->channel, 'Spotify playlist tracks imported: ' . $spotifyPlaylist->name . ' [' . $total . ' tracks]');
            } else {
                Logger::log('info', $this->channel, 'Spotify playlists tracks (playlist hasn\'t changed) ' . $spotifyPlaylist->name);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
