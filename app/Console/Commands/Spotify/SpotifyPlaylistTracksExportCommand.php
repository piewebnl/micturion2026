<?php

namespace App\Console\Commands\Spotify;

use App\Models\Playlist\Playlist;
use App\Services\Logger\Logger;
use App\Services\Spotify\Exporters\SpotifyPlaylistImageExporter;
use App\Services\Spotify\Exporters\SpotifyPlaylistTracksExporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:SpotifyPlaylistTracksExport
class SpotifyPlaylistTracksExportCommand extends Command
{
    protected $signature = 'command:SpotifyPlaylistTracksExport';

    private string $channel;

    private int $perPage = 50;

    public function handle()
    {
        $this->channel = 'spotify_playlists_tracks_export';

        Logger::deleteChannel($this->channel);

        $api = (new SpotifyApiConnect)->getApi();
        if (!$api) {
            Logger::log('error', $this->channel, 'No valid spotify API connection', [], $this);

            return;
        }

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }

        $search = config('spotify')['playlists_to_export_to_spotify'];

        $playlists = Playlist::whereIn('name', $search)->orWhereIn('parent_name', $search)->get();

        $this->output->progressStart(count($playlists));

        foreach ($playlists as $playlist) {

            $playlist = Playlist::find($playlist->id);

            $spotifyPlaylistTracksExporter = new SpotifyPlaylistTracksExporter($api, $playlist, $this->perPage);
            $lastPage = $spotifyPlaylistTracksExporter->getLastPage();

            for ($page = 1; $page <= $lastPage; $page++) {
                $spotifyPlaylistTracksExporter = new SpotifyPlaylistTracksExporter($api, $playlist, $this->perPage);
                $spotifyPlaylistTracksExporter->export($page);
            }

            $filename = basename($playlist->name);

            $destImage = env('PLAYLIST_ARTWORK_PATH_TO_IMAGES') . '/' . $filename . '.jpeg';
            if (file_exists($destImage)) {
                $spotifyPlaylistImageExporter = new SpotifyPlaylistImageExporter($api, $playlist);
                $spotifyPlaylistImageExporter->export($destImage);
            }
            $this->output->progressAdvance();
        }

        // (new SpotifyPlaylist())->deleteNotChanged();

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
