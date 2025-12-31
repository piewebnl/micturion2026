<?php

namespace App\Models\Spotify;

use App\Models\Music\Song;
use App\Models\SongSpotifyTrack\SongSpotifyTrack;
use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;

// Spotify tracks are retrieved from spotify and are stored in the database (succes or warning)
class SpotifyTrack extends Model
{
    use GlobalScopesTrait;

    protected $guarded = [];

    public function songSpotifyTrack()
    {
        return $this->hasOne(SongSpotifyTrack::class, 'spotify_track_id', 'id');
    }

    public function song()
    {
        return $this->hasOneThrough(
            Song::class,
            SongSpotifyTrack::class,
            'spotify_track_id', // Foreign key on song_spotify_track
            'id',               // Foreign key on songs
            'id',               // Local key on spotify_tracks
            'song_id'           // Local key on song_spotify_track
        );
    }

    public function store(SpotifyTrack $spotifyTrack)
    {

        $result = SpotifyTrack::updateOrCreate(
            [
                'spotify_api_track_id' => $spotifyTrack['spotify_api_track_id'],
            ],
            [
                'artist' => $spotifyTrack['artist'],
                'album' => $spotifyTrack['album'],
                'album_sanitized' => $spotifyTrack['album_sanitized'],
                'name' => $spotifyTrack['name'],
                'name_sanitized' => $spotifyTrack['name_sanitized'],
                'track_number' => $spotifyTrack['track_number'],
                'disc_number' => $spotifyTrack['disc_number'],
                'spotify_api_album_id' => $spotifyTrack['spotify_api_album_id'],
                'artwork_url' => $spotifyTrack['artwork_url'],
            ]
        );

        return $result;
    }

    public function getSpotifyTracksWithSong($filterValues)
    {
        return SpotifyTrack::select(
            'spotify_tracks.id as spotify_track_id',
            'spotify_tracks.name as spotify_track_name',
            // 'spotify_tracks.uri as spotify_track_uri',

            'songs.id as song_id',
            'songs.name as song_name',
            'songs.album_artist as album_artist',
            'songs.disc_number as disc_number',
            'songs.disc_count as disc_count',
            'songs.track_number as track_number',

            'albums.id as album_id',
            'albums.name as album_name',
            'albums.sort_name as album_sort_name',
            'albums.persistent_id as persistent_album_id',
            'albums.year as album_year',
            'albums.rating as album_rating',
            'albums.category_id as category_id',

            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',

            'song_spotify_track.id as song_spotify_track_id'
        )
            ->join('song_spotify_track', 'song_spotify_track.spotify_track_id', '=', 'spotify_tracks.id')
            ->join('songs', 'songs.id', '=', 'song_spotify_track.song_id')
            ->join('albums', 'songs.album_id', '=', 'albums.id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->orderBy('artists.sort_name')
            ->orderBy('albums.sort_name')
            ->orderBy('albums.year')
            ->orderBy('songs.disc_number')
            ->orderBy('songs.track_number')
            ->whereId($filterValues, 'category_id', 'categories')
            ->whereId($filterValues, 'album_id', 'album_id')
            ->customPaginateOrLimit($filterValues);
    }
}
