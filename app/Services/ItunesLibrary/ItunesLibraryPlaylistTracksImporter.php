<?php

namespace App\Services\ItunesLibrary;

use App\Helpers\PaginationHelper;
use App\Models\ItunesLibrary\ItunesLibrary;
use App\Models\ItunesLibrary\ItunesLibraryPlaylistTrack;
use App\Models\Music\Song;
use App\Models\Playlist\Playlist;
use App\Models\Playlist\PlaylistTrack;
use App\Traits\Logger\Logger;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

// Import iTunes library playlist tracks (needs parsing) to db, 'playlist' favourite is stored to 'songs' table
class ItunesLibraryPlaylistTracksImporter
{
    private $itunesLibrary;

    private $importFavourite = false;

    private $playlist;

    private $parsedPlaylist;

    private $tracks; // tracks from a parsed xml library playlist

    private $total = 0;

    private $perPage = 0;

    private $page = 1;

    private $lastPage = null;

    private // $response;

    private $resource = [];

    public function __construct(ItunesLibrary $itunesLibrary, Playlist $playlist, int $perPage)
    {
        $this->itunesLibrary = $itunesLibrary;
        $this->playlist = $playlist;
        $this->perPage = $perPage;

        $itunesLibraryPlaylistsParser = new ItunesLibraryPlaylistsParser($this->itunesLibrary);
        $this->parsedPlaylist = $itunesLibraryPlaylistsParser->getPlaylistByPersistentId($this->playlist->persistent_id, true);

        if (count($this->parsedPlaylist) == 0) {
            Logger::log('error', 'itunes_library_import_playlist_tracks', 'iTunes library playlist empty ' . $this->playlist->name);

            return false;
        }

        // Set order (APARTE FUNCTIE)
        $count = 0;
        $start = ($this->page - 1) * $this->perPage;
        foreach ($this->parsedPlaylist['tracks'] as $playlistTrack) {
            $playlistTrack['order'] = $start + $count;
            $this->tracks[] = $playlistTrack;
            $count++;
        }

        $this->total = count($this->tracks);
        $this->calculateLastPage();
    }

    public function importPerPage(int $page)
    {
        $this->page = $page;

        // SPLIT OTHER LOGIC
        if ($this->importFavourite) {

            // Erase all favourite first time
            if ($page == 1) {
                DB::table('songs')->where('favourite', true)->update(['favourite' => null]);
            }

            foreach ($this->tracks as $track) {
                Song::where('persistent_id', $track['persistent_id'])->update(['favourite' => true]);
            }

            $this->resource = [
                'page' => $this->page,
                'total_playlist_tracks' => $this->total,
                'total_playlist_tracks_imported' => count($this->tracks),
            ];
        } else {

            $itunesLibraryPlaylistTrack = new ItunesLibraryPlaylistTrack;
            $itunesLibraryPlaylistTrack->storeAll($this->playlist->id, $this->tracks);

            $this->resource = [
                'page' => $this->page,
                'total_playlist_tracks' => $this->total,
                'total_playlist_tracks_imported' => count($this->tracks),
                'playlist_tracks' => $itunesLibraryPlaylistTrack->getResource(),
            ];
        }

        // Only log latest page
        if ($this->page == $this->lastPage) {

            $playlistTrack = new PlaylistTrack;
            $playlistTrack->deleteNotChanged($this->playlist);

            if ($this->importFavourite) {
                Logger::log('info', 'itunes_library_import_tracks_favourite', 'iTunes library favourite tracks imported: ' . $this->total);
            } else {
                Logger::log('info', 'itunes_library_import_playlist_tracks', 'iTunes library playlists tracks imported: ' . $this->playlist->name . ': ' . $this->total);
            }
        }

        $this->response = response()->success('Playlist tracks imported', $this->resource);
    }

    public function setImportFavourite(bool $importFavourite)
    {
        $this->importFavourite = $importFavourite;
    }

    private function calculateLastPage()
    {
        $this->lastPage = PaginationHelper::calculateLastPage($this->total, $this->perPage);
    }

    public function getLastPage(): ?int
    {

        return $this->lastPage;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function getResponse(): JsonResponse
    {

        return $this->response;
    }
}
