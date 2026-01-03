<?php

namespace App\Services\Spotify\Importers;

use App\Services\Logger\Logger;
use App\Models\Spotify\SpotifyPlaylist;
use App\Services\SpotifyApi\Mappers\SpotifyApiPlaylistMapper;
use App\Services\SpotifyApi\Getters\SpotifyApiUserPlaylistGetter;

// Import spotify playlists to db
class SpotifyPlaylistsImporter
{
    private $api;

    private $spotifyPlaylistGetter;

    private $spotifyApiPlaylistMapper;

    private $spotifyApiPlaylists;

    private $totalPlaylists;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $channel = 'spotify_playlists_import';

    private $resource = [];

    public function __construct($api, int $perPage)
    {
        $this->api = $api;
        $this->perPage = $perPage;
        $this->spotifyPlaylistGetter = new SpotifyApiUserPlaylistGetter($this->api, $this->perPage);
        $this->lastPage = $this->spotifyPlaylistGetter->getLastPage();
        $this->spotifyApiPlaylistMapper = new SpotifyApiPlaylistMapper;
    }

    public function import(int $page)
    {
        $this->page = $page;

        $this->spotifyApiPlaylists = [];
        $this->spotifyApiPlaylists = $this->spotifyPlaylistGetter->getPerPage($this->page);

        $this->totalPlaylists = $this->spotifyPlaylistGetter->getTotal();

        $importedPlaylists = $this->markSnapshotIdChanges($this->spotifyApiPlaylists) ?? [];

        foreach ($importedPlaylists as $spotifyApiPlaylist) {

            $playlists = $this->spotifyApiPlaylistMapper->toSpotifyPlaylist(
                $spotifyApiPlaylist,
                $spotifyApiPlaylist->snapshot_id_has_changed
            );

            SpotifyPlaylist::updateOrCreate(
                ['spotify_api_playlist_id' => $spotifyApiPlaylist->id],
                $playlists
            );
        }

        if (!empty($importedPlaylists)) {
            foreach ($importedPlaylists as $playlist) {
                Logger::log('notice', $this->channel, 'Spotify playlists imported: ' . $playlist->name . ' [' . $playlist->tracks->total . ' tracks]');
            }
        }

        $this->resource = [
            'page' => $this->page,
            'total_playlists' => $this->totalPlaylists,
            'total_playlists_imported' => count($importedPlaylists),
        ];
    }

    private function markSnapshotIdChanges(array $spotifyApiPlaylists): array
    {
        foreach ($spotifyApiPlaylists as $key => $spotifyApiPlaylist) {
            $spotifyApiPlaylists[$key]->snapshot_id_has_changed = $this->hasSnapshotIdChanged(
                $spotifyApiPlaylist->id,
                $spotifyApiPlaylist->snapshot_id
            );
        }

        return $spotifyApiPlaylists;
    }

    private function hasSnapshotIdChanged(string $spotifyApiPlaylistId, string $snapshotId): bool
    {
        $storedSnapshotId = SpotifyPlaylist::where('spotify_api_playlist_id', $spotifyApiPlaylistId)
            ->pluck('snapshot_id')
            ->first();

        return $snapshotId !== $storedSnapshotId;
    }



    public function getLastPage(): ?int
    {
        return $this->lastPage;
    }

    public function getResource()
    {
        return $this->resource;
    }
}
