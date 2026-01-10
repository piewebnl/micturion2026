<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Music\Song;
use App\Models\Spotify\SpotifySearchResultTrack;
use App\Models\Spotify\SpotifySearchTrack;
use App\Models\Spotify\SpotifyTrackCustomId;
use App\Services\Spotify\Importers\SpotifyTrackImporter;
use Illuminate\Http\JsonResponse;

// Search for spotify custom ids in table and get the spotify track via its api
class SpotifyTrackCustomIdSearcher
{
    private $api;

    private $spotifySearchResultTrack;

    private $response;

    private $resource;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function search(SpotifySearchTrack $spotifySearchTrack)
    {

        $spotifyTrackCustomId = SpotifyTrackCustomId::where('persistent_id', $spotifySearchTrack['persistent_id'])->first();
        $song = Song::where('persistent_id', $spotifySearchTrack['persistent_id'])->first();

        if ($spotifyTrackCustomId && $song) {

            $spotifyTrackImporter = new SpotifyTrackImporter($this->api);
            $spotifyTrackImporter->import($spotifyTrackCustomId['spotify_api_track_custom_id'], $song);

            // Mhaw NAAR CONVERTER
            $this->spotifySearchResultTrack = new SpotifySearchResultTrack;
            $this->spotifySearchResultTrack->fill([
                'spotify_api_track_id' => $spotifyTrackCustomId['spotify_api_track_custom_id'],
                'name' => $spotifyTrackCustomId['name'],
                'album' => $spotifyTrackCustomId['album'],
                'artist' => $spotifyTrackCustomId['artists'],
                'year' => $spotifySearchTrack['year'],
                'track_number' => $spotifySearchTrack['track_number'],
                'disc_number' => $spotifySearchTrack['disck_number'],
                'score' => 100,
                // 'artwork_url' => null,
                'status' => 'success',
                'search_name' => $spotifySearchTrack['name'],
                'search_album' => $spotifySearchTrack['album'],
                'search_artist' => $spotifySearchTrack['artist'],
                'song_id' => $spotifySearchTrack['song_id'],
            ]);
            $this->resource = $this->spotifySearchResultTrack;
            $this->response = response()->error('Spotify search result NOT FOUND', $this->resource->toArray());

            return;
        }

        // Empty result
        $this->spotifySearchResultTrack = new SpotifySearchResultTrack;
        $this->resource = $this->spotifySearchResultTrack;

        $this->response = response()->success('Spotify search result ', $this->resource->toArray());
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }

    }
