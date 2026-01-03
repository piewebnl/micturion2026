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

    private array $allNewPlaylistIds = [];

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

        $playlistsToImport = $this->determineSnapshotIdChanges($this->spotifyApiPlaylists) ?? [];

        foreach ($playlistsToImport as $spotifyApiPlaylist) {


            if ($spotifyApiPlaylist->snapshot_id_has_changed) {

                $playlists = $this->spotifyApiPlaylistMapper->toSpotifyPlaylist(
                    $spotifyApiPlaylist,
                    $spotifyApiPlaylist->snapshot_id_has_changed
                );

                SpotifyPlaylist::updateOrCreate(
                    ['spotify_api_playlist_id' => $spotifyApiPlaylist->id],
                    $playlists
                );

                Logger::log('notice', $this->channel, 'Spotify playlist imported: ' . $spotifyApiPlaylist->name . ' [' . $spotifyApiPlaylist->tracks->total . ' tracks]');
            } else {
                Logger::log('info', $this->channel, 'Spotify playlist not imported (no change): ' . $spotifyApiPlaylist->name . ' [' . $spotifyApiPlaylist->tracks->total . ' tracks]');
            }
        }


        $newPlaylistIds = collect($playlistsToImport)->pluck('id')->filter()->values()->all();
        $this->addNewPlaylistIds($newPlaylistIds);

        $this->resource = [
            'page' => $this->page,
            'new_playlist_ids' => $newPlaylistIds,
            'total_playlists' => $this->totalPlaylists,
        ];
    }

    // Add to the api playlists if the snapshot ids have changed
    private function determineSnapshotIdChanges(array $spotifyApiPlaylists): array
    {
        foreach ($spotifyApiPlaylists as $key => $spotifyApiPlaylist) {
            $spotifyApiPlaylists[$key]->snapshot_id_has_changed = $this->hasSnapshotIdChanged(
                $spotifyApiPlaylist->id,
                $spotifyApiPlaylist->snapshot_id
            );
        }

        return $spotifyApiPlaylists;
    }


    public function deleteOldPlaylists(array $oldPlaylistIds): void
    {
        if (empty($this->allNewPlaylistIds)) {
            return;
        }

        $oldPlaylistsNotInNew = array_diff($oldPlaylistIds, $this->allNewPlaylistIds);
        if (!empty($oldPlaylistsNotInNew)) {
            SpotifyPlaylist::whereIn('spotify_api_playlist_id', $oldPlaylistsNotInNew)->delete();
        }
    }

    private function addNewPlaylistIds(array $newPlaylistIds): void
    {
        $this->allNewPlaylistIds = array_values(array_unique(array_merge(
            $this->allNewPlaylistIds,
            $newPlaylistIds
        )));
    }


    private function hasSnapshotIdChanged(string $spotifyApiPlaylistId, string $snapshotId): bool
    {
        $storedSnapshotId = SpotifyPlaylist::where('spotify_api_playlist_id', $spotifyApiPlaylistId)
            ->pluck('snapshot_id')
            ->first();

        if ($snapshotId !== $storedSnapshotId) {
            return true;
        };
        return false;
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
