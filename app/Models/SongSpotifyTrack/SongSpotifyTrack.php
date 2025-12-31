<?php

namespace App\Models\SongSpotifyTrack;

use App\Models\Music\Song;
use App\Models\Spotify\SpotifyTrack;
use App\Scopes\GlobalScopesTrait;
use App\Scopes\SongSpotifyTrackScopesTrait;
use Illuminate\Database\Eloquent\Model;

// Relation table between songs and spotify tracks
class SongSpotifyTrack extends Model
{
    use GlobalScopesTrait;
    use SongSpotifyTrackScopesTrait;

    protected $guarded = [];

    protected $table = 'song_spotify_track';

    public function song()
    {
        return $this->hasOne(Song::class, 'id', 'song_id');
    }

    public function spotifyTrack()
    {
        return $this->hasOne(SpotifyTrack::class, 'id', 'spotify_track_id');
    }

    public function store(SongSpotifyTrack $songSpotifyTrack)
    {

        $result = SongSpotifyTrack::updateOrCreate(
            [
                'song_id' => $songSpotifyTrack['song_id'],
            ],
            [
                'spotify_track_id' => $songSpotifyTrack['spotify_track_id'],
                'score' => $songSpotifyTrack['score'],
                'status' => $songSpotifyTrack['status'],
                'search_artist' => $songSpotifyTrack['search_artist'],
                'search_album' => $songSpotifyTrack['search_album'],
                'search_name' => $songSpotifyTrack['search_name'],
            ]
        );

        return $result;
    }

    public function getSongSpotifyTrackWithSong(int $id, array $filterValues = [])
    {
        $filterValues['id'] = $id;

        return $this->getSongSpotifyTracksWithSongs($filterValues)->first();
    }

    public function getSongSpotifyTracksWithSongs(array $filterValues)
    {

        $all = SongSpotifyTrack::select(
            'song_spotify_track.id as id',
            'song_spotify_track.status as status',
            'song_spotify_track.search_artist as artist',
            'song_spotify_track.search_album as album',
            'song_spotify_track.search_name as name',
            'song_spotify_track.score as score',
            'spotify_tracks.id as spotify_track_id',
            'spotify_tracks.artist as spotify_track_artist',
            'spotify_tracks.album as spotify_track_album',
            'spotify_tracks.name as spotify_track_name',
            'spotify_tracks.track_number as spotify_track_number',
            'spotify_tracks.spotify_api_track_id as spotify_track_spotify_api_track_id',
            'spotify_tracks.spotify_api_album_id as spotify_track_spotify_api_album_id',
            'songs.id as song_id',
            'songs.name as song_name',
            'songs.track_number as song_track_number',
            'albums.id as album_id',
            'albums.name as album_name',
            'albums.year as album_year',
            'album_images.slug as album_image_slug',
            'album_images.largest_width as album_image_largest_width',
            'album_images.hash as album_image_hash',
            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',

        )->leftJoin('spotify_tracks', 'song_spotify_track.spotify_track_id', '=', 'spotify_tracks.id')
            ->join('songs', 'song_spotify_track.song_id', '=', 'songs.id')
            ->join('albums', 'songs.album_id', '=', 'albums.id')
            ->leftjoin('album_images', 'album_images.album_id', '=', 'albums.id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->whereId($filterValues, 'song_spotify_track.status', 'status')
            ->whereNotNull('song_spotify_track.id')
            ->whereId($filterValues, 'song_spotify_track.id', 'id')
            ->songSpotifyTrackWhereKeyword($filterValues)
            ->orderBy('artists.sort_name')
            ->orderBy('albums.sort_name')
            ->orderBy('songs.disc_number')
            ->orderBy('songs.track_number')
            ->customPaginateOrLimit($filterValues);

        return $all;
    }
}
