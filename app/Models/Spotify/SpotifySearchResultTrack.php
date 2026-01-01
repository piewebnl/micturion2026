<?php

namespace App\Models\Spotify;

use App\Models\SongSpotifyTrack\SongSpotifyTrack;
use App\Traits\Converters\ToSongSpotifyTrackConverter;
use App\Traits\Converters\ToSpotifyTrackConverter;
use Illuminate\Database\Eloquent\Model;

// Pseudo model for best match after spotify track search (spotify track + status)
class SpotifySearchResultTrack extends Model
{
    use ToSongSpotifyTrackConverter;
    use ToSpotifyTrackConverter;

    protected $guarded = [];

    private $response;

    private $resource = [];

    public function store(SpotifySearchResultTrack $spotifySearchResultTrack)
    {

        // Store the track
        $spotifyTrackModel = new SpotifyTrack;
        $spotifyTrack = $this->convertSpotifySearchResultTrackToSpotifyTrack($spotifySearchResultTrack);
        // $response = $spotifyTrackModel->store($spotifyTrack);

        // Store relation
        $songSpotifyTrackModel = new SongSpotifyTrack;
        $spotifySearchResultTrack->spotify_track_id = // $response->id;
            $songSpotifyTrack = $this->convertSpotifySearchResultTrackToSongSpotifyTrack($spotifySearchResultTrack);
        $songSpotifyTrackModel->store($songSpotifyTrack);

        return $response;
    }
}
