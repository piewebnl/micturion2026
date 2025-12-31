<?php

namespace App\Livewire\Wishlist;

use App\Livewire\Forms\Wishlist\WishlistSearchFormInit;
use App\Traits\Forms\SearchForm;
use Livewire\Component;

class WishlistSearch extends Component
{
    use SearchForm;

    private WishlistSearchFormInit $searchFormInit;

    public $filterValues = [];

    public $defaultFilterValues = [];

    public $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [
        ['field' => 'keyword'],
        ['field' => 'wishlist_album'],
        ['field' => 'music_store'],
    ];

    public $skipDefaults = [];

    public function boot()
    {
        $this->searchFormInit = new WishlistSearchFormInit($this->searchFormData);
        $this->defaultFilterValues = $this->searchFormInit->init([]);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);

        if ($this->filterValues != $this->defaultFilterValues) {
            $this->beenFiltered = true;
        }

        $this->setOrderIcon();
    }

    public function search()
    {
        $this->checkBeenFiltered();
        $this->countFiltersUsed();

        $this->setOrderIcon();
        $this->dispatch('wishlist-searched', $this->filterValues);
    }

    public function render()
    {

        return view('livewire.wishlist.wishlist-search');
    }
}
