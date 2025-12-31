<?php

namespace App\Models\SpotifyApi;

use App\Models\Spotify\SpotifyPlaylist;
use App\Traits\Converters\ToSpotifyPlaylistConverter;
use Illuminate\Database\Eloquent\Model;

// Playlist coming from spotify, stored in db via SpotifyPlaylist
class SpotifyApiPlaylist extends Model
{
    use ToSpotifyPlaylistConverter;

    public function updateOrCreateSingle(object $spotifyApiPlaylist)
    {
        $all = $this->convertSpotifyApiPlaylistToSpotifyPlaylist($spotifyApiPlaylist);
        SpotifyPlaylist::updateOrCreate(['spotify_api_playlist_id' => $spotifyApiPlaylist->id], $all);
    }

    public function updateOrCreateAll(array $spotifyApiPlaylists)
    {
        foreach ($spotifyApiPlaylists as $spotifyApiPlaylist) {
            $this->updateOrCreateSingle($spotifyApiPlaylist);
        }

        return count($spotifyApiPlaylists);
    }

    public function filterSnapshotIdsChanged($spotifyApiPlaylists): array
    {
        $filtered = [];
        foreach ($spotifyApiPlaylists as $spotifyApiPlaylist) {

            if ($spotifyApiPlaylist->snapshot_id_has_changed) {
                $filtered[] = $spotifyApiPlaylist;
            }
        }

        return $filtered;
    }

    public function haveSnapshotIdsChanged($spotifyApiPlaylists)
    {
        foreach ($spotifyApiPlaylists as $key => $spotifyApiPlaylist) {
            if ($this->hasSnapshotIdChanged($spotifyApiPlaylist->id, $spotifyApiPlaylist->snapshot_id)) {
                $spotifyApiPlaylists[$key]->snapshot_id_has_changed = true;
            } else {
                $spotifyApiPlaylists[$key]->snapshot_id_has_changed = false;
            }
        }

        return $spotifyApiPlaylists;
    }

    // Compare the spotify snapshot id, with the one in spotify playlist database
    public function hasSnapshotIdChanged(string $spotifyApiPlaylistId, string $snapshotId): bool
    {
        $storedSnapshotId = SpotifyPlaylist::where('spotify_api_playlist_id', $spotifyApiPlaylistId)->pluck('snapshot_id')->first();

        // dd($snapshotId . ' vs ', $storedSnapshotId);
        if ($snapshotId != $storedSnapshotId) {
            return true;
        }

        return false;
    }
}
