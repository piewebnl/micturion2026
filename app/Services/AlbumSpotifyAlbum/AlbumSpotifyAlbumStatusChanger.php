<?php

namespace App\Services\AlbumSpotifyAlbum;

use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;
use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbumCustomId;
use App\Models\Spotify\SpotifyAlbumUnavailable;
use App\Traits\Converters\ToSpotifyAlbumCustomIdConverter;
use Illuminate\Http\JsonResponse;

class AlbumSpotifyAlbumStatusChanger
{
    use ToSpotifyAlbumCustomIdConverter;

    private $response;

    private $resource = [];

    public function changeStatus(AlbumSpotifyAlbum $albumSpotifyAlbum, string $status)
    {
        $id = $albumSpotifyAlbum['id'];

        // Get album
        $album = new Album;
        $albumWithSpotifyAlbum = $album->getAlbumWithSpotifyAlbum($albumSpotifyAlbum['album_id']);

        if (!$albumWithSpotifyAlbum) {
            dd('No spotify album');
        }

        $spotifyAlbumCustomId = $this->convertAlbumToSpotifyAlbumCustomId($albumWithSpotifyAlbum);

        // dd($spotifyAlbumCustomId);

        $spotifyAlbumCustomIdModel = new SpotifyAlbumCustomId;
        $spotifyAlbumUnavailable = new SpotifyAlbumUnavailable;

        if ($status == 'error') {
            $albumSpotifyAlbum = AlbumSpotifyAlbum::find($id);
            $albumSpotifyAlbum->status = 'error';
            $albumSpotifyAlbum->score = 0;
            $albumSpotifyAlbum->save();

            // Delete custom ID
            $found = SpotifyAlbumCustomId::where('persistent_id', $spotifyAlbumCustomId['persistent_id'])->first();
            if ($found) {
                $spotifyAlbumCustomIdModel->destroy($found['id']);
            }

            // Add to unavailable table
            // NAAR CONVERTER

            $spotifyAlbumUnavailable->fill([
                'persistent_id' => $albumWithSpotifyAlbum['persistent_id'],
                'artist' => $albumWithSpotifyAlbum['artist_name'],
                'name' => $albumWithSpotifyAlbum['name'],
            ]);

            $spotifyAlbumUnavailableModel = new SpotifyAlbumUnavailable;
            $spotifyAlbumUnavailableModel->store($spotifyAlbumUnavailable);
        }

        if ($status == 'success') {
            $albumSpotifyAlbum = AlbumSpotifyAlbum::find($id);
            $albumSpotifyAlbum->status = $status;
            $albumSpotifyAlbum->score = 100;
            $albumSpotifyAlbum->save();

            // Delete from Unavailable
            $found = SpotifyAlbumUnavailable::where('persistent_id', $albumWithSpotifyAlbum['persistent_id'])->first();
            if ($found) {
                $spotifyAlbumUnavailable->destroy($found['id']);
            }

            // Store custom ID
            $spotifyAlbumCustomIdModel->store($spotifyAlbumCustomId);
        }

        $albumSpotifyAlbum = new AlbumSpotifyAlbum;
        $this->resource[] = $albumSpotifyAlbum->getAlbumSpotifyAlbumWithAlbum($id);

        $this->response = response()->success('Marked as ' . $status, $this->resource);
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
