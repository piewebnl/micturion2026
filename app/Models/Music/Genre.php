<?php

namespace App\Models\Music;

use App\Models\ItunesLibrary\ItunesLibraryTrack;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use QueryCache;

    protected $guarded = [];

    public function songs()
    {
        return $this->has(Song::class);
    }

    public function storeGenre(ItunesLibraryTrack $itunesTrack)
    {

        if ($itunesTrack->genre != null) {
            $genre = Genre::firstOrNew(
                ['name' => $itunesTrack->genre]
            );
            $genre->save();

            return $genre->id;
        }
    }

    public function getAllGenres()
    {

        $genres = $this->getCache('get-all-genres');

        if (!$genres) {
            $genres = Genre::orderBy('name')->get();
            $this->setCache('get-all-genres', [], $genres);
        }

        return $genres;
    }
}
