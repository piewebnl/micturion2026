<?php

namespace App\Models\ItunesLibrary;

use App\Models\Music\Album;
use App\Models\Music\Artist;
use App\Models\Music\Category;
use App\Models\Music\Format;
use App\Models\Music\Genre;
use App\Models\Music\SkippedSong;
use App\Models\Music\Song;
use App\Services\ItunesLibrary\ItunesLibraryTrackMutator;
use App\Services\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

// Pseudo model of iTunes Track
class ItunesLibraryTrack extends Model
{
    protected $guarded = [];

    private $resource = [];

    public function storeTracks(array $tracks)
    {
        foreach ($tracks as $track) {
            $this->storeTrack((array) $track);
        }
    }

    public function storeTrack(array $track)
    {

        $thisMutator = new ItunesLibraryTrackMutator;
        $thisMutator->doMutate((array) $track);
        $track = $thisMutator->getTrack();

        // Not valid for import (store in skipped_songs)
        if ($track['status'] != 'success') {

            $this->fill($track);

            $song = new SkippedSong;
            $this->id = $song->storeItunesLibraryTrack($this);
            unset($song);

            // Add some info for response
            $this->resource[] = [
                'status' => 'success',
                'ok' => true,
                'text' => 'Succesfully imported skipped track',
                'id' => $this->id,
                'persistent_id' => $this->persistent_id,
                'name' => $this->name,
                'album' => $this->album,
                'artist' => $this->artist,
            ];

            Logger::log('info', 'itunes_library_import_tracks', 'Skipped tracked importerd: ' . $this->artist . ' - ' . $this->album . ' - ' . $this->name);

            /*
            try {
                $this->resource[] = [
                    'status' => $track['status'],
                    'text' => $track['text'],
                    'persistent_id' => $track['persistent_id'],
                    'name' => $track['name'],
                    'album' => $track['album'],
                    'artist' => $track['artist'],
                ];

                Logger::log('warning', 'itunes_library_import_tracks', 'Track not importerd: ' . $track['text']);
            } catch (Exception $e) {
                Logger::log('error', 'itunes_library_import_tracks', 'Track not importerd: ' . $track['text'] . ' ' . $e->getMessage());
            }
            */

            return;
        }

        $this->fill($track);

        $artist = new Artist;
        $this->artist_id = $artist->storeItunesLibraryTrack($this);
        unset($artist);

        $category = new Category;
        $this->category_id = $category->storeItunesLibraryTrack($this);
        unset($category);

        $format = new Format;
        $this->format_ids = $format->storeFormats($this->formats);
        unset($format);

        $genre = new Genre;
        $this->genre_id = $genre->storeGenre($this);
        unset($genre);

        $album = new Album;
        $this->album_id = $album->storeItunesLibraryTrack($this);
        unset($album);

        $song = new Song;
        $this->id = $song->storeItunesLibraryTrack($this);
        unset($song);

        // Add some info for response
        $this->resource[] = [
            'status' => 'success',
            'ok' => true,
            'text' => 'Succesfully imported',
            'id' => $this->id,
            'persistent_id' => $this->persistent_id,
            'name' => $this->name,
            'album' => $this->album,
            'artist' => $this->artist,
        ];

        Logger::log('info', 'itunes_library_import_tracks', 'Track importerd: ' . $this->artist . ' - ' . $this->album . ' - ' . $this->name);
    }

    public function getResource(): array
    {
        return $this->resource;
    }
}
