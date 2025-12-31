<?php

namespace App\Livewire\Spotify;

use App\Livewire\Forms\Spotify\SpotifyReviewSearchFormInit;
use App\Traits\Forms\SearchForm;
use Livewire\Attributes\On;
use Livewire\Component;

class SpotifyReviewSearch extends Component
{
    use SearchForm;

    private SpotifyReviewSearchFormInit $searchFormInit;

    public ?array $filterValues = [];

    public ?array $defaultFilterValues = [];

    public ?array $oldFilterValues = [];

    public array $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [
        ['field' => 'keyword'],
    ];

    public $skipDefaults = [];

    public function boot()
    {
        $this->searchFormInit = new SpotifyReviewSearchFormInit($this->searchFormData);
        $this->defaultFilterValues = $this->searchFormInit->init([]);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
        $this->oldFilterValues = $this->filterValues;

        $this->checkBeenFiltered();
        $this->setOrderIcon();
    }

    /*
    #[On('spotify-review-search-set-filter')]
    public function setFilter($field, $value)
    {
        $this->filterValues[$field] = $value;
        $this->search();
    }
*/

    #[On('spotify-review-set-page')]
    public function setPage($page)
    {
        $this->filterValues['page'] = $page;
        $this->search();
    }

    public function search()
    {
        // Reset page if view switch
        if ($this->filterValues['view'] != $this->oldFilterValues['view']) {
            //  $filterValues['page'] = 1;
        }

        $this->checkBeenFiltered();
        $this->countFiltersUsed();

        $this->setOrderIcon();

        $this->dispatch('spotify-review-searched', $this->filterValues);

        $this->oldFilterValues = $this->filterValues;
    }

    public function render()
    {

        $this->countFiltersUsed();

        return view('livewire.spotify.spotify-review-search');
    }
}
