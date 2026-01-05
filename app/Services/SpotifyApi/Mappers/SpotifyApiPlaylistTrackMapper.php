<?php

namespace App\Services\SpotifyApi\Mappers;

use Illuminate\Support\Str;

class SpotifyApiPlaylistTrackMapper
{
    public function toSpotifyTrack(object $spotifyApiPlaylistTrack): array
    {
        return [
            'spotify_api_track_id' => $spotifyApiPlaylistTrack->id,
            'artist' => $spotifyApiPlaylistTrack->artists[0]->name,
            'name' => $spotifyApiPlaylistTrack->name,
            'album' => $spotifyApiPlaylistTrack->album->name,
            'track_number' => $spotifyApiPlaylistTrack->track_number,
            'disc_number' => $spotifyApiPlaylistTrack->disc_number,
            'spotify_api_album_id' => $spotifyApiPlaylistTrack->album->id,
            'artwork_url' => $spotifyApiPlaylistTrack?->album?->images[0]?->url ?? null,
        ];
    }

    /*
    public function normalizePlaylistTrack(object $spotifyApiPlaylistTrack): object
    {
        if ($spotifyApiPlaylistTrack->id === null) {
            $spotifyApiPlaylistTrack->id = $this->generateFakeSpotifyTrackId($spotifyApiPlaylistTrack);
            $spotifyApiPlaylistTrack->is_found = false;
        }

        return $spotifyApiPlaylistTrack;
    }
        */

    /*
    private function generateFakeSpotifyTrackId(object $spotifyApiPlaylistTrack): string
    {
        $value = $spotifyApiPlaylistTrack->artists[0]->name
            . $spotifyApiPlaylistTrack->album->name
            . $spotifyApiPlaylistTrack->name;

        return 'NOTEXIST' . Str::limit(md5($value), 14, '');
    }
    */
}
