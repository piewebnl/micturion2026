<?php

namespace App\Services\Spotify\Importers;

use App\Models\Music\Song;
use App\Models\Spotify\SpotifySearchResultTrack;
use App\Models\Spotify\SpotifyTrack;
use App\Models\Spotify\SpotifyTrackUnavailable;
use App\Services\Spotify\Searchers\SpotifyTrackCustomIdSearcher;
use App\Services\Spotify\Searchers\SpotifyTrackSearcher;
use App\Services\Spotify\Searchers\SpotifyTrackSearchPrepare;
use App\Traits\Converters\ToSpotifyTrackConverter;
use Illuminate\Http\JsonResponse;

// Search for spotify track via unavailable, custom ID in DB or spotify api and then import to db
class SpotifyTrackSearchAndImporter
{
    // use ToSpotifyTrackConverter;

    private $api;

    private $response;

    private $resource = [];

    private $spotifySearchResultTrack; // Best found spotify track

    private $song;

    private $spotifySearchTrack;

    public function __construct($api)
    {
        $this->api = $api;
    }

    // REWRITE
    public function import(Song $song)
    {
        $this->song = $song;

        $spotifyTrackSearchPrepare = new SpotifyTrackSearchPrepare;
        $this->spotifySearchTrack = $spotifyTrackSearchPrepare->prepareSpotifySearchTrack($this->song);

        // Search unavailable in own DB first
        $this->searchUnavailable();

        if (!$this->spotifySearchResultTrack) {

            // Search for customId in own DB first
            $this->searchCustomId();

            // Try Spotify API to find match (if not customId)
            if (!$this->spotifySearchResultTrack->spotify_api_track_id) {
                $this->searchSpotifyApi();
            }
        }

        // All good ues the SpotifyTrackImporter?
        $this->storeSpotifySearchResultTrack();
    }

    private function storeSpotifySearchResultTrack()
    {
        $spotifySearchResultTrack = new SpotifySearchResultTrack;
        $spotifyTrack = $spotifySearchResultTrack->store($this->spotifySearchResultTrack);

        $this->resource = SpotifyTrack::with('SongSpotifyTrack.song.album.artist')->find($spotifyTrack->id)->toArray();


        $this->response = response()->error('Spotify track found very low scoring', $this->resource);
    }

    private function searchUnavailable()
    {
        $found = SpotifyTrackUnavailable::where('persistent_id', $this->spotifySearchTrack['persistent_id'])->first();
        if ($found) {
            $this->spotifySearchResultTrack = new SpotifySearchResultTrack;
            $this->spotifySearchResultTrack->fill([
                'spotify_api_track_id' => null,
                'name' => '',
                'album' => '',
                'artist' => '',
                'score' => 0,
                'status' => 'error',
                'search_name' => $found['name'],
                'search_album' => $found['album'],
                'search_artist' => $found['artist'],
                'song_id' => $this->song->id,
            ]);
        }
    }

    private function searchCustomId()
    {
        $spotifyTrackCustomIdSearcher = new SpotifyTrackCustomIdSearcher($this->api);
        $spotifyTrackCustomIdSearcher->search($this->spotifySearchTrack);
        $this->spotifySearchResultTrack = $spotifyTrackCustomIdSearcher->getSpotifySearchResultTrack();
    }

    private function searchSpotifyApi()
    {
        $spotifyTrackSearcher = new SpotifyTrackSearcher($this->api);
        $spotifyTrackSearcher->search($this->spotifySearchTrack);
        $this->spotifySearchResultTrack = $spotifyTrackSearcher->getSpotifySearchResultTrack();
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
