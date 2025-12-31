<?php

namespace App\Models\Playlist;

use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Playlist extends Model
{
    use GlobalScopesTrait;

    protected $guarded = [];

    public function PlaylistTracks()
    {
        return $this->hasMany(PlaylistTrack::class);
    }

    public function store(Playlist $playlist)
    {
        $result = Playlist::updateOrCreate(
            ['persistent_id' => $playlist->persistent_id],
            [
                'name' => $playlist->name,
                'parent_name' => $playlist->parent_name,
                'persistent_id' => $playlist->persistent_id,
                'has_changed' => true,
                'parent_persistent_id' => $playlist->parent_persistent_id,

            ]
        );

        return $result['id'];
    }

    public function getTotalPlaylists(array $filterValues): int
    {
        $filterValues['page'] = null;
        $concerts = $this->getPlaylists($filterValues);

        return count($concerts);
    }

    public function getPlaylist(int $id)
    {
        $filterValues['id'] = $id;

        return $this->getPlaylists($filterValues)->first();
    }

    public function getPlaylistByName(string $name)
    {
        $filterValues['name'] = $name;

        return $this->getPlaylists($filterValues)->first();
    }

    public function getPlaylists(array $filterValues)
    {
        return Playlist::withCount('playlistTracks')->whereId($filterValues, 'name', 'name')->orderBy('name')->customPaginateOrLimit($filterValues);
    }

    public function deleteNotChanged()
    {
        Playlist::where('has_changed', 0)->delete();
        DB::table('playlists')->update(['has_changed' => 0]);
    }
}
