<?php

namespace App\Services\Spotify\Searchers;

use App\Models\SongSpotifyTrack\SongSpotifyTrack;
use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Music\SongFinder;

// Match a spotify track with song from itunes library
class SpotifyPlaylistTrackSongSearcher
{
    private $songFinder;

    public function __construct()
    {
        $this->songFinder = new SongFinder;
    }

    public function search($spotifyPlaylist)
    {

        $tracks = SpotifyPlaylist::with([
            'spotifyPlaylistTracks' => function ($q) {
                $q->whereDoesntHave('spotifyTrack.song');
            },
            'spotifyPlaylistTracks.spotifyTrack.song.album.artist',
        ])
            ->where('id', $spotifyPlaylist->id)
            ->first();

        foreach ($tracks->spotifyPlaylistTracks as $playlistTrack) {
            $this->searchTrack($playlistTrack);
        }
    }

    public function searchTrack($spotifyTrack)
    {

        $result = $this->songFinder->findMatch(
            $spotifyTrack->spotifyTrack->name,
            $spotifyTrack->spotifyTrack->album,
            $spotifyTrack->spotifyTrack->artist,
            75
        );

        // NAAR CONERTER
        if (isset($result['song']['id'])) {
            $songSpotifyTrack = new SongSpotifyTrack;
            $songSpotifyTrack->song_id = $result['song']['id'];
            $songSpotifyTrack->spotify_track_id = $spotifyTrack->spotifyTrack->id;
            $songSpotifyTrack->search_artist = $spotifyTrack->spotifyTrack->name;
            $songSpotifyTrack->search_album = $spotifyTrack->spotifyTrack->album;
            $songSpotifyTrack->search_name = $spotifyTrack->spotifyTrack->artist;
            $songSpotifyTrack->status = $result['status'];
            $songSpotifyTrack->score = $result['score'];
            $songSpotifyTrack->store($songSpotifyTrack);
        }
    }
}
