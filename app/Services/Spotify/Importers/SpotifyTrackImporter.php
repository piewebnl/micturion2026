<?php

namespace App\Services\Spotify\Importers;

use App\Models\Music\Song;
use App\Models\Spotify\SpotifySearchResultTrack;
use App\Models\Spotify\SpotifySearchTrack;
use App\Models\Spotify\SpotifyTrack;
use App\Models\Spotify\SpotifyTrackCustomId;
use App\Services\SpotifyApi\Getters\SpotifyApiTrackGetter;
use App\Traits\Converters\ToSpotifySearchResultTrackConverter;
use App\Traits\Converters\ToSpotifyTrackConverter;
use App\Traits\Converters\ToSpotifyTrackCustomIdConverter;
use Illuminate\Http\JsonResponse;

// Import a spotify track by a given spotify track id and a song
class SpotifyTrackImporter
{
    use ToSpotifySearchResultTrackConverter;
    use ToSpotifyTrackConverter;
    use ToSpotifyTrackCustomIdConverter;

    private $api;

    private // $response;

    private $resource = [];

    private $song;

    private $spotifySearchResultTrack;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function import(string $spotifyApiTrackId, Song $song)
    {
        $this->song = $song;

        // Get spotify track via api directly
        $spotifyTrackGetter = new SpotifyApiTrackGetter($this->api);
        $spotifyApiTrack = $spotifyTrackGetter->get($spotifyApiTrackId);

        if (!$spotifyApiTrack) {
            return;
        }

        // Some info we need
        $spotifySearchTrack = new SpotifySearchTrack;
        $spotifySearchTrack->fill(
            [
                'search_name' => $song->name,
                'search_album' => $song->album,
                'search_artist' => $song->artist,
                'song_id' => $song->id,
            ]
        );

        $score['total'] = 100; // direct hit

        $this->spotifySearchResultTrack = $this->convertSpotifyApiTrackToSpotifySearchResultTrack($spotifyApiTrack, $score, 'success', $spotifySearchTrack);

        $spotifyTrackCustomId = $this->convertSpotifyApiTrackToSpotifyTrackCustomId($spotifyApiTrack, $song);

        $spotifyTrackCustomIdModel = new SpotifyTrackCustomId;
        $spotifyTrackCustomIdModel->store($spotifyTrackCustomId);

        $this->storeSpotifyTrackValid();

    }

    private function storeSpotifyTrackValid()
    {
        $spotifySearchResultTrack = new SpotifySearchResultTrack;
        $spotifyTrack = $spotifySearchResultTrack->store($this->spotifySearchResultTrack);

        $this->resource = SpotifyTrack::with('SongSpotifyTrack.song.album.artist')->find($spotifyTrack->id)->toArray();

        if ($this->resource['song_spotify_track']['status'] == 'success') {
            $this->response = response()->success('Spotify track found', $this->resource);

            return;
        }
        if ($this->resource['song_spotify_track']['status'] == 'warning') {
            $this->response = response()->warning('Spotify track found (low scoring)', $this->resource);

            return;
        }
        $this->response = response()->warning('Spotify track found very low scoring', $this->resource);
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
