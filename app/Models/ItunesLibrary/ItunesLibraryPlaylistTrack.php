<?php

namespace App\Models\ItunesLibrary;

use App\Models\Music\Song;
use App\Models\Playlist\PlaylistTrack;
use App\Traits\Converters\ToPlaylistTrackConverter;
use Illuminate\Database\Eloquent\Model;

// Pseudo model of iTunes Playlist Track
class ItunesLibraryPlaylistTrack extends Model
{
    // use ToPlaylistTrackConverter;

    protected $guarded = [];

    private $resource = [];

    public function storeAll(int $playlistId, array $itunesLibraryPlaylistTracks): void
    {

        foreach ($itunesLibraryPlaylistTracks as $itunesLibraryPlaylistTrack) {
            $this->store($playlistId, $itunesLibraryPlaylistTrack);
        }
    }

    public function store(int $playlistId, array $itunesLibraryPlaylistTrack): ?PlaylistTrack
    {

        // Does the song exists?
        $song = Song::where('persistent_id', $itunesLibraryPlaylistTrack['persistent_id'])->first();

        if (!$song) {
            return null;
        }

        $playlistTrack = PlaylistTrack::updateOrCreate([
            'playlist_id' => $playlistId,
            'song_id' => $song['id'],
            'order' => $itunesLibraryPlaylistTrack['order'],
            'has_changed' => false,
        ]);

        $this->resource[] = [
            'status' => 'success',
            'ok' => true,
            'text' => 'Playlist Imported',
            'id' => $itunesLibraryPlaylistTrack['id'],
            'name' => $itunesLibraryPlaylistTrack['name'],
            'order' => $itunesLibraryPlaylistTrack['order'],
        ];

        return $playlistTrack;
    }

    public function getResource(): array
    {
        return $this->resource;
    }
}
