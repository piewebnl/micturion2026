<?php

namespace App\Traits\Converters;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait ToSpotifyPlaylistConverter
{
    public function convertSpotifyApiPlaylistToSpotifyPlaylist(object $spotifyApiPlaylist): array
    {
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

    // Playlist 2021 - 02 Feb
    private function getDateFromName(string $name)
    {
        if (Str::startsWith($name, 'Playlist 20')) {
            $year = substr($name, 9, 4);
            $month = substr($name, 16, 2);

            return Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-01');
        }
    }
}
