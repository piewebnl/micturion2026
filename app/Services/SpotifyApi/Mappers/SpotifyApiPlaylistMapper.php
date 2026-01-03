<?php

namespace App\Services\SpotifyApi\Mappers;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SpotifyApiPlaylistMapper
{

    public function toSpotifyPlaylist(object $spotifyApiPlaylist, bool $snapshotChanged): array
    {
        $spotifyApiPlaylist->snapshot_id_has_changed = $snapshotChanged;

        return [
            'spotify_api_playlist_id' => $spotifyApiPlaylist->id,
            'name' => $spotifyApiPlaylist->name,
            'url' => $spotifyApiPlaylist->external_urls->spotify,
            'tracks_url' => $spotifyApiPlaylist->tracks->href,
            'snapshot_id' => $spotifyApiPlaylist->snapshot_id,
            'snapshot_id_has_changed' => $spotifyApiPlaylist->snapshot_id_has_changed,
            'total_tracks' => $spotifyApiPlaylist->tracks->total,
            'date' => $this->getDateFromName($spotifyApiPlaylist->name),
            'has_changed' => true,
        ];
    }

    private function getDateFromName(string $name)
    {
        if (Str::startsWith($name, 'Playlist 20')) {
            $year = substr($name, 9, 4);
            $month = substr($name, 16, 2);

            return Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-01');
        }
    }
}
