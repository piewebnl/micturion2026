<?php

namespace App\Livewire\LastFm;

use App\Livewire\Forms\LastFm\LastFmSearchFormInit;
use App\Traits\Forms\SearchForm;
use Livewire\Component;

class LastFmSearch extends Component
{
    use SearchForm;

    private LastFmSearchFormInit $searchFormInit;

    public ?array $filterValues = [];

    public array $searchFormData;

    public function boot()
    {
        $this->searchFormInit = new LastFmSearchFormInit($this->searchFormData);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
    }

    public function search()
    {
        $this->dispatch('last-fm-searched', $this->filterValues);
        $this->skipRender();
    }

    public function render()
    {
        return view('livewire.last-fm.last-fm-search');
    }
}
