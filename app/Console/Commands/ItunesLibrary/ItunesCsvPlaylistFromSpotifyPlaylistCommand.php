<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Models\Spotify\SpotifyPlaylist;
use App\Services\ItunesLibrary\ItunesLibraryCsvPlaylistFromSpotifyPlaylist;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:ItunesCsvPlaylistFromSpotifyPlaylist
class ItunesCsvPlaylistFromSpotifyPlaylistCommand extends Command
{
    protected $signature = 'command:ItunesCsvPlaylistFromSpotifyPlaylistCommand';

    private string $channel = 'itunes_csv_playlist_from_spotify_playlist';

    public function handle()
    {

        Logger::deleteChannel($this->channel);

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }

        $spotifyPlaylistsToExport = config('ituneslibrary.itunes_csv_playlist_from_spotify_playlist');

        $spotifyPlaylists = SpotifyPlaylist::where(function ($q) use ($spotifyPlaylistsToExport) {
            foreach ($spotifyPlaylistsToExport as $name) {
                $q->orWhere('name', 'like', '%' . $name . '%');
            }
        })->get();

        if (count($spotifyPlaylists) == 0) {
            Logger::log('info', $this->channel, 'No spotfy playlists (yet) to make iTunes csv');
            // Logger::echo($this->channel);

            return;
        }

        $this->output->progressStart(count($spotifyPlaylists));

        $tunesLibraryCsvPlaylistFromSpotifyPlaylist = new ItunesLibraryCsvPlaylistFromSpotifyPlaylist;

        foreach ($spotifyPlaylists as $sp) {
            $tunesLibraryCsvPlaylistFromSpotifyPlaylist->makeCsvPlaylist($sp);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
