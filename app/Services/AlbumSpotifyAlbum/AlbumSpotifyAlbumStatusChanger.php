<?php

namespace App\Services\AlbumSpotifyAlbum;

use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyAlbumCustomId;
use App\Models\Spotify\SpotifyAlbumUnavailable;
use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;

class AlbumSpotifyAlbumStatusChanger
{
    public function changeStatus(AlbumSpotifyAlbum $albumSpotifyAlbum, string $status)
    {
        $id = $albumSpotifyAlbum['id'];

        // Get album
        $album = new Album;
        $albumWithSpotifyAlbum = $album->getAlbumWithSpotifyAlbum($albumSpotifyAlbum['album_id']);

        if (!$albumWithSpotifyAlbum) {
            return;
        }

        $spotifyAlbumUnavailable = new SpotifyAlbumUnavailable;

        if ($status === 'error') {
            $albumSpotifyAlbum = AlbumSpotifyAlbum::find($id);
            $albumSpotifyAlbum->status = 'unavailable';
            $albumSpotifyAlbum->score = 0;
            $albumSpotifyAlbum->save();

            // Delete custom ID
            $found = SpotifyAlbumCustomId::where('persistent_id', $albumWithSpotifyAlbum->persistent_id)->first();
            if ($found) {
                SpotifyAlbumCustomId::destroy($found['id']);
            }

            SpotifyAlbum::updateOrCreate(
                ['id' => $albumSpotifyAlbum->spotify_album_id],
                [
                    'spotify_api_album_id' => null,
                    'name' => 'NOT FOUND',
                    'name_sanitized' => null,
                    'artist' => 'NOT FOUND',
                    'artist_sanitized' => null,
                ]
            );

            $spotifyAlbumUnavailable->fill([
                'persistent_id' => $albumWithSpotifyAlbum['persistent_id'],
                'artist' => $albumWithSpotifyAlbum['artist_name'],
                'name' => $albumWithSpotifyAlbum['name'],
            ]);

            $spotifyAlbumUnavailableModel = new SpotifyAlbumUnavailable;
            $spotifyAlbumUnavailableModel->store($spotifyAlbumUnavailable);
        } elseif ($status === 'success') {
            $albumSpotifyAlbum = AlbumSpotifyAlbum::find($id);
            $albumSpotifyAlbum->status = 'custom';
            $albumSpotifyAlbum->score = 100;
            $albumSpotifyAlbum->save();

            // Delete from Unavailable
            $found = SpotifyAlbumUnavailable::where('persistent_id', $albumWithSpotifyAlbum['persistent_id'])->first();
            if ($found) {
                $spotifyAlbumUnavailable->destroy($found['id']);
            }

            SpotifyAlbumCustomId::updateOrCreate(
                [
                    'persistent_id' => $albumWithSpotifyAlbum['persistent_id'],
                ],
                [
                    'spotify_api_album_custom_id' => $albumWithSpotifyAlbum['spotify_api_album_id'],
                    'artist' => $albumWithSpotifyAlbum['artist_name'],
                    'name' => $albumWithSpotifyAlbum['name'],
                ]
            );
        }

        // Reload
        $albumSpotifyAlbum = new AlbumSpotifyAlbum;
    }
}
