<?php

namespace App\Services\Spotify\Changers;

use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyAlbumCustomId;
use App\Models\Spotify\SpotifyAlbumUnavailable;

class SpotifyAlbumStatusChanger
{
    public function changeStatus(SpotifyAlbum $albumSpotifyAlbum, string $status)
    {
        $id = $albumSpotifyAlbum['id'];

        $album = new Album;
        $albumWithSpotifyAlbum = $album->getAlbumWithSpotifyAlbum($albumSpotifyAlbum['album_id']);

        if (!$albumWithSpotifyAlbum) {
            return;
        }

        if ($status === 'error') {
            $this->handleErrorStatus($id, $albumWithSpotifyAlbum);
        } elseif ($status === 'success') {
            $this->handleSuccessStatus($id, $albumWithSpotifyAlbum);
        }

        // Reload
        $albumSpotifyAlbum = new SpotifyAlbum;
    }

    private function handleErrorStatus(int $id, $albumWithSpotifyAlbum): void
    {
        $albumSpotifyAlbum = SpotifyAlbum::find($id);
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
                'artwork_url' => null,
            ]
        );

        $spotifyAlbumUnavailable = new SpotifyAlbumUnavailable;
        $spotifyAlbumUnavailable->fill([
            'persistent_id' => $albumWithSpotifyAlbum['persistent_id'],
            'artist' => $albumWithSpotifyAlbum['artist_name'],
            'name' => $albumWithSpotifyAlbum['name'],
        ]);

        $spotifyAlbumUnavailableModel = new SpotifyAlbumUnavailable;
        $spotifyAlbumUnavailableModel->store($spotifyAlbumUnavailable);
    }

    private function handleSuccessStatus(int $id, $albumWithSpotifyAlbum): void
    {
        $albumSpotifyAlbum = SpotifyAlbum::find($id);
        $albumSpotifyAlbum->status = 'custom';
        $albumSpotifyAlbum->score = 100;
        $albumSpotifyAlbum->save();

        // Delete from Unavailable
        $spotifyAlbumUnavailable = new SpotifyAlbumUnavailable;
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
}
