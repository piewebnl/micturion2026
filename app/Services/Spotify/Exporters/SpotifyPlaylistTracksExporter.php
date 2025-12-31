<?php

namespace App\Services\Spotify\Exporters;

use App\Helpers\PaginationHelper;
use App\Models\Playlist\Playlist;
use App\Models\Playlist\PlaylistTrack;
use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Spotify\Creators\SpotifyPlaylistCreator;
use App\Services\SpotifyApi\Deleters\SpotifyApiPlaylistEmptier;
use App\Services\SpotifyApi\Posters\SpotifyApiPlaylistTracksPoster;
use App\Traits\Logger\Logger;
use Illuminate\Http\JsonResponse;

// Export playlist with tracks to spotify
class SpotifyPlaylistTracksExporter
{
    private $api;

    private // $response;

    private $playlist;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $resource = [];

    private $playlistTracks = [];

    private $spotifyIds = [];

    private string $channel;

    private int $totalSpotifyPlaylistTracks;

    private $spotifyPlaylistTracks = [];

    public function __construct($api, Playlist $playlist, int $perPage)
    {
        $this->api = $api;
        $this->playlist = $playlist;
        $this->perPage = $perPage;
        $this->channel = 'spotify_playlists_tracks_export';
    }

    public function export(int $page)
    {
        $this->page = $page;

        $this->getPlaylistTracks();
        $this->determineSpotifyIds();
        $this->calculateLastPage();

        $spotifyPlaylist = new SpotifyPlaylist;
        $foundSpotifyPlaylist = $spotifyPlaylist->getSpotifyPlaylistByName($this->playlist->name);

        // Create playlist if not found
        if (!$foundSpotifyPlaylist) {

            $spotifyPlaylistCreator = new SpotifyPlaylistCreator($this->api);
            $spotifyPlaylistCreator->create($this->playlist->name);
            Logger::log('info', $this->channel, 'Spotify Playlist created: ' . $this->playlist->name);

            // Reload it
            $foundSpotifyPlaylist = $spotifyPlaylist->getSpotifyPlaylistByName($this->playlist->name);
        }

        $spotifyPlaylistEmptier = new SpotifyApiPlaylistEmptier($this->api);
        $spotifyPlaylistEmptier->empty($foundSpotifyPlaylist['spotify_api_playlist_id']);

        if (!$this->totalSpotifyPlaylistTracks) {
            $this->response = response()->error('No spotify Playlist Tracks to export: ' . $foundSpotifyPlaylist['name']);
        }
        $spotifyPlaylistTracksPoster = new SpotifyApiPlaylistTracksPoster($this->api);
        $spotifyPlaylistTracksPoster->postPerPage($foundSpotifyPlaylist['spotify_api_playlist_id'], $this->spotifyIds, $this->page);

        $this->resource = [
            'playlist' => $this->playlist,
            'spotify_playlist' => $foundSpotifyPlaylist,

        ];

        if ($this->page == $this->lastPage) {
            Logger::log('info', $this->channel, 'Spotify Playlist Tracks exported: ' . $foundSpotifyPlaylist['name'] . ' [' . $this->totalSpotifyPlaylistTracks . ' tracks]');
        }

        $this->response = response()->success('Spotify Playlist Tracks exported: ' . $foundSpotifyPlaylist['name'], $this->resource);
    }

    private function getPlaylistTracks()
    {
        // Get all the playlistTracks per playlist
        $playlistTrack = new PlaylistTrack;

        $this->playlistTracks = $playlistTrack->getSpotifyTracksPerPage([
            'page' => $this->page,
            'per_page' => $this->perPage,
            'playlist_id' => $this->playlist->id,

        ]);
    }

    private function determineSpotifyIds()
    {
        foreach ($this->playlistTracks as $playlistTrack) {
            $this->determineSpotifyId($playlistTrack);
        }
        $this->totalSpotifyPlaylistTracks = count($this->spotifyIds);
    }

    private function determineSpotifyId(PlaylistTrack $playlistTrack)
    {

        // Custom Spotify Ids
        if ($playlistTrack['songSpotifyTrack']['spotify_custom_id'] != '') {
            $this->spotifyIds[] = $playlistTrack['SongSpotifyTrack']['spotify_custom_id'];
            $this->spotifyPlaylistTracks[] = [
                'status' => 'success',
                'resource' => [
                    'playlist_track' => $playlistTrack,
                ],
                'text' => 'Exported with custom ID: ' . $playlistTrack['song']['name'],
            ];

            return;
        }

        // Searched spotify ids
        if ($playlistTrack['SongSpotifyTrack']['spotifyTrack']['spotify_api_track_id'] != '') {
            $this->spotifyIds[] = $playlistTrack['SongSpotifyTrack']['spotifyTrack']['spotify_api_track_id'];
            $this->spotifyPlaylistTracks[] = [
                'status' => 'success',
                'resource' => [
                    'playlist_track' => $playlistTrack,
                ],
                'text' => 'Exported: ' . $playlistTrack['song']['name'],
            ];

            return;
        }

        // Nothing
        $this->spotifyPlaylistTracks[] = [
            'status' => 'error',
            'resource' => [
                'playlist_track' => $playlistTrack,
            ],
            'text' => 'Not exported' . $playlistTrack['song']['name'],
        ];
    }

    private function calculateLastPage()
    {
        $this->lastPage = PaginationHelper::calculateLastPage($this->totalSpotifyPlaylistTracks, $this->perPage);
    }

    public function getLastPage(): ?int
    {
        $this->getPlaylistTracks();
        $this->determineSpotifyIds();
        $this->calculateLastPage();

        return $this->lastPage;
    }

    public function getTotalSpotifyPlaylistTracks(): ?int
    {
        return $this->totalSpotifyPlaylistTracks;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
