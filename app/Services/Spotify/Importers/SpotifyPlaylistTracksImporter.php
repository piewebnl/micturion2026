<?php

namespace App\Services\Spotify\Importers;

use App\Models\Spotify\SpotifyPlaylist;
use App\Models\Spotify\SpotifyPlaylistTrack;
use App\Models\Spotify\SpotifyTrack;
use App\Services\SpotifyApi\Getters\SpotifyApiUserPlaylistTracksGetter;
use App\Services\SpotifyApi\Mappers\SpotifyApiPlaylistTrackMapper;

// Import spotify playlist tracks to db
class SpotifyPlaylistTracksImporter
{
    private $api;

    private SpotifyApiUserPlaylistTracksGetter $spotifyPlaylistTracksGetter;

    private SpotifyPlaylist $spotifyPlaylist;

    private SpotifyApiPlaylistTrackMapper $spotifyApiPlaylistTrackMapper;

    private $total;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private $resource = [];

    private array $allNewPlaylistTrackIds = [];

    public function __construct($api, SpotifyPlaylist $spotifyPlaylist, int $perPage)
    {
        $this->api = $api;
        $this->perPage = $perPage;
        $this->spotifyPlaylist = $spotifyPlaylist;
        $this->spotifyPlaylistTracksGetter = new SpotifyApiUserPlaylistTracksGetter($this->api, $this->spotifyPlaylist, $this->perPage);
        $this->lastPage = $this->spotifyPlaylistTracksGetter->getLastPage();
        $this->total = $this->spotifyPlaylist->total_tracks;
        $this->spotifyApiPlaylistTrackMapper = new SpotifyApiPlaylistTrackMapper;
    }

    public function import(int $page)
    {
        $this->page = $page;

        // Get spotify tracks from spotify api
        $spotifyApiPlaylistTracks = [];
        $spotifyApiPlaylistTracks = $this->spotifyPlaylistTracksGetter->getPerPage($this->page, $this->perPage);

        $newPlaylistTrackIds = [];
        foreach ($spotifyApiPlaylistTracks as $order => $spotifyApiPlaylistTrack) {

            // NEEDED?
            // $spotifyApiPlaylistTrack = $this->spotifyApiPlaylistTrackMapper
            //  ->normalizePlaylistTrack($spotifyApiPlaylistTrack);

            // Save Spotify Track
            $spotifyTrackData = $this->spotifyApiPlaylistTrackMapper
                ->toSpotifyTrack($spotifyApiPlaylistTrack);

            $spotifyTrack = SpotifyTrack::updateOrCreate(
                ['spotify_api_track_id' => $spotifyTrackData['spotify_api_track_id']],
                $spotifyTrackData
            );

            SpotifyPlaylistTrack::updateOrCreate(
                [
                    'spotify_playlist_id' => $this->spotifyPlaylist->id,
                    'spotify_track_id' => $spotifyTrack->id,
                ],
                [
                    'order' => $order,
                ]
            );

            $newPlaylistTrackIds[] = $spotifyTrack->id;
        }

        $this->addNewPlaylistTrackIds($newPlaylistTrackIds);

        // Update the playlist snapshot if changed
        if ($this->page == $this->lastPage) {
            SpotifyPlaylist::where('id', $this->spotifyPlaylist->id)->update(['snapshot_id_has_changed' => false]);
        }

        $this->resource = [
            'page' => $page,
            'messages' => [],
            'total' => $this->total,
        ];
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function deleteOldPlaylistTracks(array $oldPlaylistTrackIds): void
    {
        if (empty($this->allNewPlaylistTrackIds)) {
            return;
        }

        $oldPlaylistTracksNotInNew = array_diff($oldPlaylistTrackIds, $this->allNewPlaylistTrackIds);
        if (!empty($oldPlaylistTracksNotInNew)) {
            SpotifyPlaylistTrack::where('spotify_playlist_id', $this->spotifyPlaylist->id)
                ->whereIn('spotify_track_id', $oldPlaylistTracksNotInNew)
                ->delete();
        }
    }

    private function addNewPlaylistTrackIds(array $newPlaylistTrackIds): void
    {
        $this->allNewPlaylistTrackIds = array_values(array_unique(array_merge(
            $this->allNewPlaylistTrackIds,
            $newPlaylistTrackIds
        )));
    }
}
