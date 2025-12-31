<?php

namespace App\Livewire\Music;

use App\Livewire\Forms\Music\AlbumWithoutDiscogsSearchFormInit;
use App\Models\Discogs\DiscogsRelease;
use App\Models\Discogs\DiscogsReleaseCustomId;
use App\Models\Music\Album;
use Livewire\Component;

class AlbumWithoutDiscogsResults extends Component
{
    private AlbumWithoutDiscogsSearchFormInit $albumWithoutDiscogsSearchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public $filterValues = [];

    public $searchFormData;

    public $perPage = 100;

    public function boot()
    {
        $this->albumWithoutDiscogsSearchFormInit = new AlbumWithoutDiscogsSearchFormInit($this->searchFormData);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->albumWithoutDiscogsSearchFormInit->init($this->filterValues);
    }

    public function skipDiscogsRelease($albumId)
    {
        // get the album
        $album = Album::where('id', $albumId)->with('artist', 'formats')->first();

        if ($album) {

            // delete any old associations
            $discogsRelease = DiscogsRelease::where('album_id', $albumId)->delete();

            $discogsRelease = new DiscogsRelease;
            $discogsRelease->fill([
                'album_id' => $album['id'],
                'release_id' => 0, // 0 = skip
                'artist' => $album['artist']['name'],
                'title' => $album['name'],
                'score' => 0,
                'format' => $format ?? '',
            ]);
            $discogsRelease->save();

            // Update the custom ID
            $discogsReleaseCustomId = new DiscogsReleaseCustomId;
            $discogsReleaseCustomId->fill([
                'release_id' => 'skipped',
                'persistent_album_id' => $album['persistent_id'],
                'artist' => $album['artist']['name'],
                'title' => $album['name'],
            ]);
            $discogsReleaseCustomId->store($discogsReleaseCustomId);
        } else {
            dd('Album not found: ' . $albumId);
        }
    }

    public function render()
    {
        $album = new Album;
        $loadedAlbums = $album->getAlbumsWithoutdiscogsRelease($this->filterValues);
        $albums = $loadedAlbums->toArray()['data'];

        return view('livewire.music.album-without-discogs-results', [
            'filterValues' => $this->filterValues,
            'albums' => $albums,
            'loadedAlbums' => $loadedAlbums,
        ]);

        return view('livewire.music.album-without-discogs-results', ['showNotes' => $this->showNotes]);
    }
}
