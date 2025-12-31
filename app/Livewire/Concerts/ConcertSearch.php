<?php

namespace App\Livewire\Concerts;

use App\Livewire\Forms\Concerts\ConcertSearchFormInit;
use App\Traits\Forms\SearchForm;
use App\Traits\QueryCache\QueryCache;
use Livewire\Component;

class ConcertSearch extends Component
{
    use QueryCache;
    use SearchForm;

    private ConcertSearchFormInit $searchFormInit;

    // #[Url(as: 's', keep: false, history: true)]
    public $filterValues = [];

    public $defaultFilterValues = [];

    public $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [
        ['field' => 'venue'],
        ['field' => 'festival'],
        ['field' => 'name'],
        ['field' => 'year'],
        ['field' => 'keyword'],
        ['field' => 'view', 'skip' => true],
    ];

    public $data = null;

    public function loadData()
    {
        // Fetch data or perform any action
        // $this->preFetchFilterValues = $this->filterValues;
    }

    public function boot()
    {
        $this->searchFormInit = new ConcertSearchFormInit($this->searchFormData);
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
        $this->dispatch('concerts-searched', $this->filterValues);
    }

    public function render()
    {
        return view('livewire.concerts.concert-search');
    }
}
