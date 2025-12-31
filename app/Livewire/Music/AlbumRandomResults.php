<?php

namespace App\Livewire\Music;

use App\Livewire\Forms\Music\AlbumRandomSearchFormInit;
use App\Models\Music\Artist;
use App\Traits\Forms\SearchForm;
use Livewire\Attributes\On;
use Livewire\Component;

class AlbumRandomResults extends Component
{
    use SearchForm;

    private AlbumRandomSearchFormInit $searchFormInit;

    public $filterValues = [];

    public $searchFormData;

    private $picked = false;

    public $ids = [];

    public function boot()
    {
        $this->searchFormInit = new AlbumRandomSearchFormInit($this->searchFormData);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
    }

    #[On('album-random-searched')]
    public function searched($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    public function remove(int $id)
    {
        $this->filterValues['album_id'] = $this->ids;
        $index = array_search($id, $this->filterValues['album_id']);
        unset($this->filterValues['album_id'][$index]);
        $this->ids = $this->filterValues['album_id'];
        $this->picked = true;
    }

    public function render()
    {

        if (!$this->picked) {
            $artist = new Artist;
            $artists = $artist->getArtistsWithAlbums($this->filterValues);
            $this->ids = $artists->pluck('album_id')->toArray();
        } else {
            $this->filterValues['sort'] = 'name';
            $artist = new Artist;
            $artists = $artist->getArtistsWithAlbums($this->filterValues);
        }

        $ids = $this->ids;

        return view('livewire.music.album-random-results', compact('artists', 'ids'));
    }
}
