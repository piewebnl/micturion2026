<?php

namespace App\Livewire\Tiermaker;

use App\Livewire\Forms\Tiermaker\TiermakerSearchFormInit;
use App\Models\Music\Artist;
use App\Traits\Forms\SearchForm;
use App\Traits\QueryCache\QueryCache;
use Livewire\Component;

class TiermakerSearch extends Component
{
    use QueryCache;
    use SearchForm;

    private TiermakerSearchFormInit $searchFormInit;

    public $filterValues = [];

    public $artists = [];

    public $newkeyword = '';

    public $defaultFilterValues = [];

    public $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [];

    public $data = null;

    public function boot()
    {
        $this->searchFormInit = new TiermakerSearchFormInit($this->searchFormData);
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
        $this->dispatch('tiermaker-searched', $this->filterValues);
    }

    public function searchNew()
    {
        $this->artists = Artist::where('name', 'like', '%' . $this->newkeyword . '%')->get();
    }

    public function render()
    {

        return view('livewire.tiermaker.tiermaker-search', ['filterValues' => $this->filterValues, 'artists' => $this->artists]);
    }
}
