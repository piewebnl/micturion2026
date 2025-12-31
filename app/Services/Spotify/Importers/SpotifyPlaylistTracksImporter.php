<?php

namespace App\Services\Spotify\Importers;

use App\Models\Spotify\SpotifyPlaylist;
use App\Models\SpotifyApi\SpotifyApiPlaylistTrack;
use App\Services\SpotifyApi\Getters\SpotifyApiUserPlaylistTracksGetter;
use Illuminate\Http\JsonResponse;

// Import spotify playlist tracks to db
class SpotifyPlaylistTracksImporter
{
    private $api;

    private SpotifyApiUserPlaylistTracksGetter $spotifyPlaylistTracksGetter;

    private SpotifyPlaylist $spotifyPlaylist;

    private array $spotifyApiPlaylistTracks;

    private $spotifyApiPlaylistTrack;

    private $total;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private // $response;

    private $resource = [];

    public function __construct($api, SpotifyPlaylist $spotifyPlaylist, int $perPage)
    {
        $this->api = $api;
        $this->perPage = $perPage;
        $this->spotifyPlaylist = $spotifyPlaylist;
        $this->spotifyPlaylistTracksGetter = new SpotifyApiUserPlaylistTracksGetter($this->api, $this->spotifyPlaylist, $this->perPage);
        $this->spotifyApiPlaylistTrack = new SpotifyApiPlaylistTrack;
        $this->lastPage = $this->spotifyPlaylistTracksGetter->getLastPage();
        $this->total = $this->spotifyPlaylist->total_tracks;
    }

    public function import(int $page)
    {
        $this->page = $page;

        // Get spotify tracks from spotify
        $this->spotifyApiPlaylistTracks = [];
        $this->spotifyApiPlaylistTracks = $this->spotifyPlaylistTracksGetter->getPerPage($this->page, $this->perPage);

        // Store or update playlist tracks and spotify tracks
        $this->spotifyApiPlaylistTrack->updateOrCreateAll($this->spotifyPlaylist, $this->spotifyApiPlaylistTracks);

        // Update the playlist snapshot if changed
        if ($this->page == $this->lastPage) {
            SpotifyPlaylist::where('id', $this->spotifyPlaylist->id)->update(['snapshot_id_has_changed' => false]);
        }

        $this->resource = [
            'page' => $page,
            'messages' => $this->spotifyApiPlaylistTrack->getResource(),
            'total' => $this->total,
        ];

        $this->response = response()->success('Spotify Playlist Tracks imported', $this->resource);
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
