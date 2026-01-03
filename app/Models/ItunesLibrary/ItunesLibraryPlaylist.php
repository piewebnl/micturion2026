<?php

namespace App\Models\ItunesLibrary;

use App\Models\Playlist\Playlist;
use App\Traits\Converters\ToPlaylistConverter;
use App\Services\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

// Pseudo model of iTunes Playlist
class ItunesLibraryPlaylist extends Model
{
    use ToPlaylistConverter;

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

        $converted = $this->convertItunesLibraryPlaylistToPlaylist($itunesLibraryPlaylist);

        $playlist = new Playlist;
        $id = $playlist->store($converted);

        $this->resource[] = [
            'status' => 'success',
            'ok' => true,
            'text' => 'Playlist Imported',
            'id' => $itunesLibraryPlaylist['id'],
            'name' => $itunesLibraryPlaylist['name'],

        ];

        Logger::log('info', 'itunes_library_import_playlists', 'iTunes library playlist imported: ' . $itunesLibraryPlaylist['name']);

        return $id;
    }

    public function getResource(): array
    {
        return $this->resource;
    }
}
