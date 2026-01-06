<?php

namespace App\Models\ItunesLibrary;

use App\Models\Playlist\Playlist;
use App\Services\Logger\Logger;
use App\Traits\Converters\ToPlaylistConverter;
use Illuminate\Database\Eloquent\Model;

// Pseudo model of iTunes Playlist
class ItunesLibraryPlaylist extends Model
{
    // use ToPlaylistConverter;

    protected $guarded = [];

    private $resource = [];

    public function storeAll(array $itunesLibraryPlaylists)
    {
        foreach ($itunesLibraryPlaylists as $itunesLibraryPlaylist) {
            $this->store($itunesLibraryPlaylist);
        }
    }

    public function store($itunesLibraryPlaylist)
    {

        $result = Playlist::storeFromItunesPlaylist($itunesLibraryPlaylist);

        $this->resource[] = [
            'status' => 'success',
            'ok' => true,
            'text' => 'Playlist Imported',
            'id' => $itunesLibraryPlaylist['id'],
            'name' => $itunesLibraryPlaylist['name'],

        ];

        Logger::log('info', 'itunes_library_import_playlists', 'iTunes library playlist imported: ' . $itunesLibraryPlaylist['name']);

        return $result->id;
    }

    public function getResource(): array
    {
        return $this->resource;
    }
}
