<?php

namespace App\Livewire\Music;

use App\Livewire\Forms\Music\MusicSearchFormInit;
use App\Traits\Forms\SearchForm;
use Livewire\Attributes\On;
use Livewire\Component;
use RalphJSmit\Livewire\Urls\Facades\Url;

class MusicSearch extends Component
{
    use SearchForm;

    private MusicSearchFormInit $searchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public ?array $filterValues = [];

    public ?array $defaultFilterValues = [];

    public array $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [
        ['field' => 'categories'],
        ['field' => 'formats'],
        ['field' => 'genres'],
        ['field' => 'compilations'],
        ['field' => 'songs'],
        ['field' => 'keyword'],
        ['field' => 'artist'],
        ['field' => 'year'],
        ['field' => 'view', 'skip' => true],
    ];

    public function boot()
    {

        $this->searchFormInit = new MusicSearchFormInit($this->searchFormData);
        $this->defaultFilterValues = $this->searchFormInit->init([]);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);

        $this->checkBeenFiltered();
        $this->setOrderIcon();
    }

    #[On('music-search-set-filter')]
    public function setFilter($field, $value)
    {
        $this->filterValues[$field] = $value;
        $this->search();
    }

    public function owned()
    {
        $this->checkAll('formats', ['None']);
    }

    public function loadLetter($startLetter)
    {
        $this->filterValues['start_letter'] = $startLetter;
        $this->search();
    }

    public function search()
    {
        $this->checkBeenFiltered();
        $this->countFiltersUsed();
        $this->setOrderIcon();
        $this->dispatch('music-searched', $this->filterValues);

        if ($this->filterValues['start_letter'] != '') {
            $this->filterValues['start_letter'] = null;
        }
    }

    public function render()
    {
        $this->countFiltersUsed();

        return view('livewire.music.music-search');
    }
}
