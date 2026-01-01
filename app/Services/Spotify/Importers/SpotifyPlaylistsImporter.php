<?php

namespace App\Services\Spotify\Importers;

use App\Models\SpotifyApi\SpotifyApiPlaylist;
use App\Services\SpotifyApi\Getters\SpotifyApiUserPlaylistGetter;
use App\Traits\Logger\Logger;
use Illuminate\Http\JsonResponse;

// Import spotify playlists to db
class SpotifyPlaylistsImporter
{
    private $api;

    private $spotifyPlaylistGetter;

    private $spotifyApiPlaylist;

    private $spotifyApiPlaylists;

    private $totalPlaylists;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $channel = '';

    private $response;

    private $resource = [];

    public function __construct($api, int $perPage)
    {
        $this->api = $api;
        $this->perPage = $perPage;
        $this->channel = 'spotify_playlists_import';
        $this->spotifyPlaylistGetter = new SpotifyApiUserPlaylistGetter($this->api, $this->perPage);
        $this->lastPage = $this->spotifyPlaylistGetter->getLastPage();
        $this->spotifyApiPlaylist = new SpotifyApiPlaylist;
    }

    public function import(int $page)
    {
        $this->page = $page;

        $this->spotifyApiPlaylists = [];
        $this->spotifyApiPlaylists = $this->spotifyPlaylistGetter->getPerPage($this->page);

        $this->totalPlaylists = $this->spotifyPlaylistGetter->getTotal();

        $importedPlaylists = $this->spotifyApiPlaylist->haveSnapshotIdsChanged($this->spotifyApiPlaylists);
        $this->spotifyApiPlaylist->updateOrCreateAll($importedPlaylists);

        if ($importedPlaylists) {
            foreach ($importedPlaylists as $playlist) {
                $resourcePlaylists[] = [
                    'status' => 'success',
                    'name' => $playlist->name,
                    'total_tracks' => $playlist->tracks->total,
                ];
                Logger::log('info', $this->channel, 'Spotify playlists imported: ' . $playlist->name . ' [' . $playlist->tracks->total . ' tracks]');
            }
        }

        $this->resource = [
            'page' => $this->page,
            'total_playlists' => $this->totalPlaylists,
            'total_playlists_imported' => count($importedPlaylists),
        ];

        $this->response = response()->success('Spotify playlists importedPlaylists', $this->resource);
    }

    public function getLastPage(): ?int
    {
        return $this->lastPage;
    }

    public function getResponse(): JsonResponse
    {

        return $this->response;
    }
}
