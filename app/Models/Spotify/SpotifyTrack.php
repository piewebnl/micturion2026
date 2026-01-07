<?php

namespace App\Models\Spotify;

use App\Models\Music\Song;
use App\Models\Music\Album;
use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;
use App\Dto\Spotify\SpotifySearchTrackResult;

// Spotify tracks are retrieved from spotify and are stored in the database (succes or warning)
class SpotifyTrack extends Model
{
    use GlobalScopesTrait;

    protected $guarded = [];


    public function song()
    {
        return $this->belongsTo(Song::class, 'artist_id');
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
                'name' => $spotifyTrack['name'],
                'track_number' => $spotifyTrack['track_number'],
                'disc_number' => $spotifyTrack['disc_number'],
                'spotify_api_album_id' => $spotifyTrack['spotify_api_album_id'],
                'artwork_url' => $spotifyTrack['artwork_url'],
                'status' => $spotifyTrack['status'] ?? 'error',
            ]
        );

        return $result;
    }

    public function getSpotifyTracksWithSong($filterValues)
    {
        return SpotifyTrack::select(
            'spotify_tracks.id as id',
            'spotify_tracks.name as name',
            'spotify_tracks.status as status',
            'spotify_tracks.track_number as track_number',
            'spotify_tracks.disc_number as disc_number',
            'spotify_tracks.score as score',
            'spotify_tracks.spotify_api_track_id as spotify_api_track_id',
            'spotify_tracks.artist as artist',
            'spotify_tracks.album as album',

            'songs.id as song_id',
            'songs.name as song_name',
            'songs.album_artist as song_album_artist',
            'songs.disc_number as song_disc_number',
            'songs.disc_count as song_disc_count',
            'songs.track_number as song_track_number',

            'albums.id as album_id',
            'albums.name as album_name',
            'albums.sort_name as album_sort_name',
            'albums.persistent_id as persistent_album_id',
            'albums.year as album_year',
            'albums.rating as album_rating',
            'albums.category_id as category_id',
            'album_images.id as album_image_id',
            'album_images.slug as album_image_slug',
            'album_images.largest_width as album_image_largest_width',
            'album_images.hash as album_image_hash',
            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',


        )
            ->join('songs', 'songs.id', '=', 'spotify_tracks.song_id')
            ->join('albums', 'songs.album_id', '=', 'albums.id')
            ->leftjoin('album_images', 'album_images.album_id', '=', 'albums.id')
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


    public function storeFromSpotifySearchResultTrack(SpotifySearchTrackResult $spotifySearchTrackResult, Song $song)
    {

        $result = SpotifyTrack::updateOrCreate(
            [
                'spotify_api_track_id' => $spotifySearchTrackResult->spotify_api_track_id,
            ],
            [
                'spotify_api_album_id' => $spotifySearchTrackResult->spotify_api_album_id,
                'song_id' => $song->id,
                'name' => $spotifySearchTrackResult->name,
                'album' => $spotifySearchTrackResult->album,
                'artist' => $spotifySearchTrackResult->artist,
                'artwork_url' => $spotifySearchTrackResult->artwork_url,
                'score' => $spotifySearchTrackResult->score,
                'track_number' => $spotifySearchTrackResult->track_number,
                'disc_number' => $spotifySearchTrackResult->disc_number,
                'search_name' => $spotifySearchTrackResult->search_name,
                'search_album' => $spotifySearchTrackResult->search_album,
                'search_artist' => $spotifySearchTrackResult->search_artist,
                'status' => $spotifySearchTrackResult->status,

            ]
        );

        return $result;
    }
}
