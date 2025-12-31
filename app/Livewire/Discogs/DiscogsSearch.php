<?php

namespace App\Livewire\Discogs;

use App\Livewire\Forms\Discogs\DiscogsSearchFormInit;
use App\Traits\Forms\SearchForm;
use App\Traits\QueryCache\QueryCache;
use Livewire\Component;

class DiscogsSearch extends Component
{
    use QueryCache;
    use SearchForm;

    private DiscogsSearchFormInit $searchFormInit;

    public $filterValues = [];

    public $defaultFilterValues = [];

    public $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [
        ['field' => 'formats'],
        ['field' => 'matched'],
        ['field' => 'show_notes'],
    ];

    public $data = null;

    public function loadData()
    {
        // Fetch data or perform any action
        // $this->preFetchFilterValues = $this->filterValues;
    }

    public function boot()
    {
        $this->searchFormInit = new DiscogsSearchFormInit($this->searchFormData);
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
        $this->dispatch('discogs-searched', $this->filterValues);
    }

    public function render()
    {
        return view('livewire.discogs.discogs-search', ['filterValues' => $this->filterValues]);
    }
}
