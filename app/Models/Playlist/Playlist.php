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

    public static function storeFromItunesPlaylist($itunesLibraryPlaylist,): self
    {
        $persistentId = data_get($itunesLibraryPlaylist, 'persistent_id');

        return static::updateOrCreate(
            ['persistent_id' => $persistentId],
            [
                'name' => data_get($itunesLibraryPlaylist, 'name'),
                'parent_name' => data_get($itunesLibraryPlaylist, 'parent_name'),
                'persistent_id' => $persistentId,
                'has_changed' => false,
                'parent_persistent_id' => data_get($itunesLibraryPlaylist, 'parent_persistent_id'),
            ]
        );
    }

    /*
    public function store(Playlist $playlist)
    {
        return static::storeFromItunesPlaylist($playlist)->id;
    }
        */

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
