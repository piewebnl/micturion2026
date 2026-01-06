<?php

namespace App\Models\Playlist;

use App\Models\Music\Song;
use App\Scopes\GlobalScopesTrait;
use DB;
use Illuminate\Database\Eloquent\Model;

class PlaylistTrack extends Model
{
    use GlobalScopesTrait;

    protected $guarded = [];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function song()
    {
        return $this->hasOne(Song::class, 'id', 'song_id');
    }



    public function store(array $playlistTrack): PlaylistTrack
    {
        $result = PlaylistTrack::updateOrCreate(
            [
                'song_id' => $playlistTrack['song_id'],
                'playlist_id' => $playlistTrack['playlist_id'],
                'order' => $playlistTrack['order'],
            ],
            [
                'playlist_id' => $playlistTrack['playlist_id'],
                'song_id' => $playlistTrack['song_id'],
                'has_changed' => true,
                'order' => $playlistTrack['order'],
            ]
        );

        return $result;
    }

    public function deleteNotChanged(Playlist $playlist): void
    {
        PlaylistTrack::where('playlist_id', $playlist->id)->where('has_changed', 0)->delete();
        DB::table('playlist_tracks')->where('playlist_id', $playlist->id)->update(['has_changed' => 0]);
    }

    public function getSpotifyTracksPerPage(array $filterValues)
    {

        return PlaylistTrack::whereHas('songSpotifyTrack', function ($q) {
            $q->where('status', '=', 'success');
        })->with(['song.album.artist', 'songSpotifyTrack' => function ($q) {
            $q->where('status', '=', 'success');
        }, 'songSpotifyTrack.spotifyTrack'])
            ->whereId($filterValues, 'playlist_id', 'playlist_id')
            ->orderBy('order')
            ->customPaginateOrLimit($filterValues);
    }
}
