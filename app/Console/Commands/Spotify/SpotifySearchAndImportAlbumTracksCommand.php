<?php

namespace App\Console\Commands\Spotify;

use App\Models\Music\Song;
use App\Models\Spotify\SpotifyAlbum;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\Spotify\Importers\SpotifyAlbumTracksImporter;
use App\Services\Spotify\Importers\SpotifyTrackSearchAndImporter;
use App\Services\Spotify\Helpers\SpotifyNameHelper;

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

        $songs = (new Song)->getSongsWithoutSpotifyTrack([
            'categories' => [1, 2],
        ]);

        $songsByAlbum = collect($songs)->groupBy('album_id');
        $spotifyNameHelper = new SpotifyNameHelper;

        $this->output->progressStart($songsByAlbum->count());

        foreach ($songsByAlbum as $albumId => $albumSongs) {
            $spotifyAlbum = SpotifyAlbum::where('album_id', $albumId)
                ->where('status', 'success')
                ->first(['spotify_api_album_id', 'name']);
            $spotifyApiAlbumId = $spotifyAlbum?->spotify_api_album_id;
            if (!$spotifyApiAlbumId) {
                Logger::log(
                    'warning',
                    $this->channel,
                    'No spotify album id with success score for album_id: ' . $albumId . ' (fallback to track search)'
                );
                foreach ($albumSongs as $song) {
                    $fullSong = Song::with('album.artist')->find($song->id);
                    if (!$fullSong) {
                        continue;
                    }

                    $spotifyTrackSearchAndImporter = new SpotifyTrackSearchAndImporter($api);
                    $spotifyTrackSearchAndImporter->import($fullSong);
                }
                $this->output->progressAdvance();
                continue;
            }

            $albumName = $albumSongs->first()?->album_name ?? null;
            if ($albumName && $spotifyAlbum?->name) {
                $albumSimilarity = $spotifyNameHelper->areNamesSimilar($albumName, $spotifyAlbum->name);
                if ($albumSimilarity < 20) {
                    Logger::log(
                        'warning',
                        $this->channel,
                        'Spotify album name mismatch for album_id: ' . $albumId . ' (fallback to track search)',
                        [
                            ['iTunes album: ' . $albumName],
                            ['Spotify album: ' . $spotifyAlbum->name],
                            ['Similarity: ' . round($albumSimilarity)],
                        ]
                    );
                    foreach ($albumSongs as $song) {
                        $fullSong = Song::with('album.artist')->find($song->id);
                        if (!$fullSong) {
                            continue;
                        }

                        $spotifyTrackSearchAndImporter = new SpotifyTrackSearchAndImporter($api);
                        $spotifyTrackSearchAndImporter->import($fullSong);
                    }
                    $this->output->progressAdvance();
                    continue;
                }
            }

            $spotifyAlbumTracksImporter = new SpotifyAlbumTracksImporter($api);
            $spotifyAlbumTracksImporter->importAlbumTracks($albumId, $spotifyApiAlbumId, $albumSongs);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
