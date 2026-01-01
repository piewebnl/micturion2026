<?php

namespace App\Services\SongSpotifyTrack;

use App\Models\Music\Song;
use App\Models\SongSpotifyTrack\SongSpotifyTrack;
use App\Models\Spotify\SpotifyTrackCustomId;
use App\Models\Spotify\SpotifyTrackUnavailable;
use App\Traits\Converters\ToSpotifyTrackCustomIdConverter;
use Illuminate\Http\JsonResponse;

// Change status of a SongSpotifyTrack and put it in CustomID or Unavailable table
class SongSpotifyTrackStatusChanger
{
    use ToSpotifyTrackCustomIdConverter;

    private $response;

    private $resource = [];

    public function changeStatus(SongSpotifyTrack $songSpotifyTrack, string $status)
    {
        $id = $songSpotifyTrack['id'];

        // Get song
        $song = new Song;
        $songWithSpotifyTrack = $song->getSongWithSpotifyTrack($songSpotifyTrack['song_id']);
        $spotifyTrackCustomId = $this->convertSongToSpotifyTrackCustomId($songWithSpotifyTrack);

        $spotifyTrackCustomIdModel = new SpotifyTrackCustomId;
        $spotifyTrackUnavailable = new SpotifyTrackUnavailable;

        if ($status == 'error') {
            $songSpotifyTrack = SongSpotifyTrack::find($id);
            $songSpotifyTrack->status = 'error';
            $songSpotifyTrack->score = 0;
            $songSpotifyTrack->save();

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
            $songSpotifyTrack = SongSpotifyTrack::find($id);
            $songSpotifyTrack->status = $status;
            $songSpotifyTrack->score = 100;
            $songSpotifyTrack->save();

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

        $songSpotifyTrack = new SongSpotifyTrack;
        $this->resource[] = $songSpotifyTrack->getSongSpotifyTrackWithSong($id);

        $this->response = response()->success('Marked as ' . $status, $this->resource);
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
