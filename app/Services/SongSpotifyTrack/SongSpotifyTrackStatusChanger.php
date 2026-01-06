<?php

namespace App\Services\SpotifyTrack;

use App\Models\Music\Song;
use App\Models\SpotifyTrack\SpotifyTrack;
use App\Models\Spotify\SpotifyTrackCustomId;
use App\Models\Spotify\SpotifyTrackUnavailable;
use App\Traits\Converters\ToSpotifyTrackCustomIdConverter;
use Illuminate\Http\JsonResponse;

// Change status of a SpotifyTrack and put it in CustomID or Unavailable table
class SpotifyTrackStatusChanger
{

    private $response;

    private $resource = [];

    public function changeStatus(SpotifyTrack $spotifyTrack, string $status)
    {
        $id = $spotifyTrack['id'];

        // Get song
        $song = new Song;
        $songWithSpotifyTrack = $song->getSongWithSpotifyTrack($spotifyTrack['song_id']);
        $spotifyTrackCustomId = $this->convertSongToSpotifyTrackCustomId($songWithSpotifyTrack);

        $spotifyTrackCustomIdModel = new SpotifyTrackCustomId;
        $spotifyTrackUnavailable = new SpotifyTrackUnavailable;

        if ($status == 'error') {
            $spotifyTrack = SpotifyTrack::find($id);
            $spotifyTrack->status = 'error';
            $spotifyTrack->score = 0;
            $spotifyTrack->save();

            // Delete custom ID
            $found = SpotifyTrackCustomId::where('persistent_id', $spotifyTrackCustomId['persistent_id'])->first();
            if ($found) {
                $spotifyTrackCustomIdModel->destroy($found['id']);
            }

            // Add to unavailable table
            // NAAR CONVERTER

            $spotifyTrackUnavailable->fill([
                'persistent_id' => $songWithSpotifyTrack['persistent_id'],
                'artist' => $songWithSpotifyTrack['artist_name'],
                'album' => $songWithSpotifyTrack['album_name'],
                'name' => $songWithSpotifyTrack['name'],
            ]);

            $spotifyTrackUnavailableModel = new SpotifyTrackUnavailable;
            $spotifyTrackUnavailableModel->store($spotifyTrackUnavailable);
        }

        if ($status == 'success') {
            $spotifyTrack = SpotifyTrack::find($id);
            $spotifyTrack->status = $status;
            $spotifyTrack->score = 100;
            $spotifyTrack->save();

            // Delete from Unavailable
            $found = SpotifyTrackUnavailable::where('persistent_id', $songWithSpotifyTrack['persistent_id'])->first();
            if ($found) {
                $spotifyTrackUnavailable->destroy($found['id']);
            }

            // Store custom ID
            if ($spotifyTrackCustomId) {
                // echo $spotifyTrackCustomId;
                $spotifyTrackCustomIdModel->store($spotifyTrackCustomId);
            }
        }

        $spotifyTrack = new SpotifyTrack;
        $this->resource[] = $spotifyTrack->getSpotifyTrackWithSong($id);

        $this->response = response()->success('Marked as ' . $status, $this->resource);
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
