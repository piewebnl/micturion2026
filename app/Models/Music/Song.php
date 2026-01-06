<?php

namespace App\Models\Music;

use App\Models\Spotify\SpotifyTrack;
use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class Song extends Model
{
    use BelongsToThrough;
    use GlobalScopesTrait;

    // iTunes field with corresponing model fields
    protected $fields = [
        'persistent_id' => 'persistent_id',
        'track_id' => 'track_id', // iTunes Track ID
        'name' => 'name',
        'rating' => 'rating',
        'time' => 'time',
        'time_ms' => 'time_ms',
        'track_number' => 'track_number',
        'track_count' => 'track_count',
        'disc_count' => 'disc_count',
        'disc_number' => 'disc_number',
        'play_count' => 'play_count',
        'favourite' => 'favourite',
        'location' => 'location',
        'grouping' => 'grouping',
        'album_id' => 'album_id',
        'album_artist' => 'album_artist',
        'sort_album_artist' => 'sort_album_artist',
        'kind' => 'kind',
        'has_changed' => 'has_changed',
        'date_added' => 'date_added',
        'date_modified' => 'date_modified',
    ];

    protected $guarded = [];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'id');
    }

    public function artist()
    {
        return $this->belongsToThrough(Artist::class, Album::class);
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function spotifyTrack()
    {
        return $this->hasOne(Song::class, 'song_id', 'id');
    }


    public function storeItunesLibraryTrack(object $itunesTrack)
    {
        if ($itunesTrack->name != null) {
            $song = Song::firstOrNew([
                'persistent_id' => $itunesTrack->persistent_id,
            ]);

            foreach ($this->fields as $key => $field) {
                $song->{$field} = $itunesTrack->{$key};
            }

            $song->save();

            return $song->id;
        }
    }

    public function getAllGroupings()
    {
        return Song::select('grouping', 'disc_count')->groupBy('grouping')->orderBy('grouping')->get();
    }

    public function getSongsWithAlbum(array $filterValues)
    {

        return Song::select(
            'songs.id as id',
            'songs.name as name',
            'songs.time as time',
            'songs.time_ms as time_ms',
            'songs.track_number as track_number',
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
            'album_images.slug as album_image_slug',
            'album_images.largest_width as album_image_largest_width',
            'album_images.hash as album_image_hash',
            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',

        )
            ->selectRaw('
                GROUP_CONCAT(DISTINCT formats.id SEPARATOR ",") as format_id,
                GROUP_CONCAT(DISTINCT formats.parent_id SEPARATOR ",") as format_parent_id,
                GROUP_CONCAT(DISTINCT IF(formats.parent_id >0, NULL, formats.name) SEPARATOR ", ") as format_name,
                GROUP_CONCAT(DISTINCT IF(formats.parent_id >0, formats.name, null) SEPARATOR ", ") as subformat_name')
            ->join('albums', 'songs.album_id', '=', 'albums.id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->leftjoin('album_images', 'album_images.album_id', '=', 'albums.id')
            ->leftjoin('album_formats', 'album_formats.album_id', '=', 'albums.id')
            ->leftjoin('formats', 'formats.id', '=', 'album_formats.format_id')
            ->orderBy('artists.sort_name')
            ->orderBy('albums.sort_name')
            ->orderBy('albums.year')
            ->orderBy('songs.disc_number')
            ->orderBy('songs.track_number')
            ->whereId($filterValues, 'format_id', 'format_ids')
            ->whereId($filterValues, 'time', 'time')
            ->whereId($filterValues, 'time_ms', 'time_ms')
            ->whereId($filterValues, 'track_number', 'track_number')
            ->whereId($filterValues, 'songs.album_id', 'album_id')
            ->groupBy('songs.id')
            ->customPaginateOrLimit($filterValues);
    }

    public function getSongsWithoutSpotifyTrack(array $filterValues)
    {

        return Song::select(
            'songs.id as id',
            'songs.name as name',
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
            'spotify_track.id as spotify_track_id'

        )->join('spotify_tracks', 'spotify_tracks.song_id', '=', 'songs.id')
            ->join('albums', 'songs.album_id', '=', 'albums.id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->orderBy('artists.sort_name')
            ->orderBy('albums.sort_name')
            ->orderBy('albums.year')
            ->orderBy('songs.disc_number')
            ->orderBy('songs.track_number')
            ->whereId($filterValues, 'category_id', 'categories')
            ->whereId($filterValues, 'album_id', 'album_id')
            ->whereNull('spotify_track_id', null)
            ->customPaginateOrLimit($filterValues);
    }

    public function getSongWithSpotifyTrack(int $id, array $filterValues = [])
    {
        $filterValues['id'] = $id;

        return $this->getSongsWithSpotifyTrack($filterValues)->first();
    }

    public function getTotalSongsWithSpotifyTrack(array $filterValues): int
    {
        $filterValues['page'] = null;
        $songs = $this->getSongsWithSpotifyTrack($filterValues);

        return count($songs);
    }

    public function getSongsWithSpotifyTrack(array $filterValues)
    {
        return Song::select(
            'songs.id as id',
            'songs.persistent_id as persistent_id',
            'songs.album_artist as album_artist',
            'songs.name as name',
            'songs.track_number as track_number',
            'songs.favourite as favourite',
            //'song_spotify_track.id as song_spotify_track_id',
            'spotify_tracks.spotify_api_track_id as spotify_api_track_id',
            'albums.name as album_name',
            'albums.sort_name as album_sort_name',
            'albums.persistent_id as persistent_album_id',
            'albums.year as album_year',
            'albums.rating as album_rating',
            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',
        )->join('spotify_tracks', 'spotify_tracks.song_id', '=', 'songs.id')
            ->join('albums', 'songs.album_id', '=', 'albums.id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->whereId($filterValues, 'songs.id', 'id')
            ->whereId($filterValues, 'songs.favourite', 'favourite')
            ->orderBy('artists.sort_name')
            ->orderBy('albums.sort_name')
            ->orderBy('albums.year')
            ->orderBy('songs.track_number')
            ->customPaginateOrLimit($filterValues);
    }
}
