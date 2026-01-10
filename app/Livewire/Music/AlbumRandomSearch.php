<?php

namespace App\Livewire\Music;

use App\Helpers\SearchFormHelper;
use App\Livewire\Forms\Music\AlbumRandomSearchFormInit;
use App\Traits\Forms\SearchForm;
use Livewire\Attributes\On;
use Livewire\Component;

class AlbumRandomSearch extends Component
{
    use SearchForm;

    private AlbumRandomSearchFormInit $searchFormInit;

    public ?array $filterValues = [];

    public ?array $defaultFilterValues = [];

    public array $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [
        ['field' => 'categories'],
        ['field' => 'formats'],
        ['field' => 'compilations'],
    ];

    public function boot()
    {
        $this->searchFormInit = new AlbumRandomSearchFormInit($this->searchFormData);
        $this->defaultFilterValues = $this->searchFormInit->init([]);
        // $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);

        $this->checkBeenFiltered();
    }

    public function owned()
    {
        $this->checkAll('formats', ['None']);
    }

    #[On('album-random-search-set-filter')]
    public function setFilter($field, $value)
    {
        if ($field == 'formats_by_label') {
            $this->filterValues['formats'] = SearchFormHelper::checkByLabel($this->searchFormData['formats'], $value);
        } else {
            $this->filterValues[$field] = $value;
        }

        $this->search();
    }

    #[On('album-random-search')]
    public function search()
    {
        $this->checkBeenFiltered();
        $this->countFiltersUsed();

        $this->dispatch('album-random-searched', $this->filterValues);
    }

    public function render()
    {
        $this->countFiltersUsed();

        return view('livewire.music.album-random-search');
    }
}
