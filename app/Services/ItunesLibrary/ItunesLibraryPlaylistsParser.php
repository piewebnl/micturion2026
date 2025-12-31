<?php

namespace App\Services\ItunesLibrary;

use App\Models\ItunesLibrary\ItunesLibrary;

// Finds playlists and tracks in iTunes Library
class ItunesLibraryPlaylistsParser
{
    private $itunesLibrary;

    private $itunesLibraryPlaylists = []; // The playlists in the iTunes xml

    private $itunesLibraryTracks = []; // The playlist tracks Ids in the iTunes xml

    private $itunesLibraryPlaylistsParsed = []; // All playlists with their basic info

    private $playlists = []; // The actual playlists with tracks (and track details)

    public function __construct(ItunesLibrary $itunesLibrary)
    {
        $this->itunesLibrary = $itunesLibrary;
        $this->itunesLibraryTracks = $this->itunesLibrary->getTracks();
        $this->itunesLibraryPlaylists = $this->itunesLibrary->getPlaylists();
    }

    public function getPlaylistsByNames(array $playlistNames, bool $includeTracks = false): ?array
    {
        $this->parsePlaylists();
        $this->getPlaylists('name', $playlistNames);
        if ($includeTracks) {
            $this->includeTracks();
        }

        return $this->playlists;
    }

    public function getPlaylistsByPersistentIds(array $playlistPersitentIds, bool $includeTracks = false): ?array
    {
        $this->parsePlaylists();
        $this->getPlaylists('persistent_id', $playlistPersitentIds);
        if ($includeTracks) {
            $this->includeTracks();
        }

        return $this->playlists;
    }

    public function getPlaylistByPersistentId(string $playlistPersistentId, bool $includeTracks = false): array
    {

        $this->parsePlaylists();

        // dd($playlistPersistentId);
        $this->getPlaylists('persistent_id', [$playlistPersistentId]);

        if ($includeTracks) {
            $this->includeTracks();
        }

        if (isset($this->playlists[0])) {
            return $this->playlists[0];
        }

        return [];
    }

    private function getPlaylists(string $searchKey = 'name', array $searchValues = [])
    {
        foreach ($this->itunesLibraryPlaylistsParsed as $playlist) {

            if (in_array($playlist[$searchKey], $searchValues)) {

                // Try and see if there are any children
                $found = $this->findPlaylistChildren($playlist['persistent_id']);
                if (!$found) {
                    $found[] = $playlist;
                }
                $this->playlists = array_merge($this->playlists, $found);
            }
        }
    }

    private function findPlaylistChildren(string $parentPersistentId): array
    {
        $children = [];

        foreach ($this->itunesLibraryPlaylistsParsed as $playlist) {
            if ($parentPersistentId == $playlist['parent_persistent_id']) {
                $children[] = [
                    'id' => $playlist['id'],
                    'name' => $playlist['name'],
                    'parent_name' => $playlist['parent_name'],
                    'persistent_id' => $playlist['persistent_id'],
                    'parent_persistent_id' => $playlist['parent_persistent_id'],
                ];
            }
        }

        return $children;
    }

    // Parse from library
    public function parsePlaylists()
    {

        foreach ($this->itunesLibraryPlaylists as $playlist) {

            if (isset($playlist->{'Name'})) {

                $playlistPersistentId = null;

                if (isset($playlist->{'Playlist Persistent ID'})) {
                    $playlistPersistentId = $playlist->{'Playlist Persistent ID'};
                    // Keep parent name
                    $parentNames[$playlistPersistentId] = $playlist->{'Name'};
                }

                $parentPersistentId = null;
                if (isset($playlist->{'Parent Persistent ID'})) {
                    $parentPersistentId = $playlist->{'Parent Persistent ID'};
                }

                $parentName = null;
                if (isset($parentNames[$parentPersistentId])) {
                    $parentName = $parentNames[$parentPersistentId];
                }

                $this->itunesLibraryPlaylistsParsed[] = [
                    'id' => $playlist->{'Playlist ID'},
                    'name' => $playlist->{'Name'},
                    'parent_name' => $parentName,
                    'persistent_id' => $playlistPersistentId,
                    'parent_persistent_id' => $parentPersistentId,
                ];
            }
        }
    }

    private function includeTracks()
    {
        foreach ($this->playlists as $key => $playlist) {
            $tracks = $this->findTracks($playlist['persistent_id']);
            $this->playlists[$key]['tracks'] = $tracks;
        }
    }

    // Find all tracks from an iTunes playlist
    public function findTracks(string $playlistPersistentId): array
    {
        $tracks = [];

        foreach ($this->itunesLibraryPlaylists as $itunesPlaylist) {
            if (isset($itunesPlaylist->{'Playlist Items'}) && $itunesPlaylist->{'Playlist Persistent ID'} == $playlistPersistentId) {
                foreach ($itunesPlaylist->{'Playlist Items'} as $item) {
                    if (isset($item->{'Track ID'})) {
                        $tracks[] = $this->findTrackDetails($item->{'Track ID'});
                    }
                }
            }
        }

        return $tracks;
    }

    private function findTrackDetails(string $trackId): ?array
    {
        foreach ($this->itunesLibraryTracks as $track) {

            if ($track->{'Track ID'} == $trackId) {
                return [
                    'id' => $trackId,
                    'name' => $track->{'Name'},
                    'persistent_id' => $track->{'Persistent ID'},
                ];
            }
        }
    }
}
