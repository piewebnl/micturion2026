<?php

namespace App\Console\Commands\Spotify;

use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Spotify\Searchers\SpotifyPlaylistTrackSongSearcher;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

// php artisan command:SpotifyPlaylistTrackSongSearch
class SpotifyPlaylistTrackSongSearchCommand extends Command
{
    protected $signature = 'command:SpotifyPlaylistTrackSongSearch';

    private string $channel;

    private int $perPage = 50;

    public function handle()
    {
        $this->channel = 'spotify_playlist_track_song_Search';

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
            Logger::log('warning', $this->channel, 'No spotify playlists to Search songs with yet.');
            // Logger::echo($this->channel);

            return;
        }

        $api = (new SpotifyApiConnect)->getApi();
        if (!$api) {
            // Logger::echo($this->channel);

            return;
        }

        $playlistsToImport = config('spotify.playlist_tracks_to_search_for_songs');

        $spotifyPlaylists = SpotifyPlaylist::where(function ($query) use ($playlistsToImport) {
            foreach ($playlistsToImport as $name) {
                $query->orWhere('name', 'like', '%' . $name . '%');
            }
        })->get();

        $this->output->progressStart(count($spotifyPlaylists));

        $onsgSearcher = new SpotifyPlaylistTrackSongSearcher;

        foreach ($spotifyPlaylists as $spotifyPlaylist) {

            $onsgSearcher->search($spotifyPlaylist);
            $this->output->progressAdvance();
        }

        Cache::flush();

        $this->output->progressFinish();
        // Logger::echo($this->channel);
    }
}
