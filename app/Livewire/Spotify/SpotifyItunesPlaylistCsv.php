<?php

namespace App\Livewire\Spotify;

use App\Models\Spotify\SpotifyPlaylist;
use Livewire\Component;

class SpotifyItunesPlaylistCsv extends Component
{
    public function submitForm() {}

    public function render()
    {
        $withSong[] = [];
        $withoutSong[] = [];

        $spotifyPlaylist = SpotifyPlaylist::with(['spotifyPlaylistTracks.spotifyTrack.song.album.artist'])
            ->where('name', '=', 'Rutger Debbie')
            ->first();

        if ($spotifyPlaylist) {
            foreach ($spotifyPlaylist->spotifyPlaylistTracks as $item) {
                if ($item->spotifyTrack->song) {
                    $withSong[] = $item->spotifyTrack;
                } else {
                    $withoutSong[] = $item->spotifyTrack;
                }
            }
            $spotifyPlaylist->setRelation('spotifyPlaylistTracksWithSong', $withSong);
            $spotifyPlaylist->setRelation('spotifyPlaylistTracksWithoutSong', $withoutSong);
        }

        return view('livewire.spotify.spotify-itunes-playlist-csv', ['spotifyPlaylist' => $spotifyPlaylist]);
    }
}
