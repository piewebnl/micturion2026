<?php

namespace App\Livewire\Music;

use App\Livewire\Forms\Music\AlbumWithoutDiscogsSearchFormInit;
use App\Traits\Forms\SearchForm;
use App\Traits\QueryCache\QueryCache;
use Livewire\Component;

class AlbumWithoutDiscogsSearch extends Component
{
    use QueryCache;
    use SearchForm;

    private AlbumWithoutDiscogsSearchFormInit $searchFormInit;

    public $filterValues = [];

    public $defaultFilterValues = [];

    public $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [
        ['field' => 'matched'],
    ];

    public $data = null;

    public function loadData()
    {
        // Fetch data or perform any action
        // $this->preFetchFilterValues = $this->filterValues;
    }

    public function boot()
    {
        $this->searchFormInit = new AlbumWithoutDiscogsSearchFormInit($this->searchFormData);
        $this->defaultFilterValues = $this->searchFormInit->init([]);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);

        $this->checkBeenFiltered();
        $this->setOrderIcon();
    }

    public function search()
    {

        $this->checkBeenFiltered();
        $this->countFiltersUsed();

        $this->setOrderIcon();
        $this->dispatch('album-without-discogs-searched', $this->filterValues);
    }

    public function render()
    {
        return view('livewire.music.album-without-discogs-search', ['filterValues' => $this->filterValues]);
    }
}
