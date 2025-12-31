<?php

namespace App\Livewire\Spotify;

use App\Livewire\Forms\Spotify\SpotifyMonthlyPlaylistSearchFormInit;
use App\Traits\Forms\SearchForm;
use Livewire\Attributes\Url;
use Livewire\Component;

class SpotifyMonthlyPlaylistSearch extends Component
{
    use SearchForm;

    private SpotifyMonthlyPlaylistSearchFormInit $searchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public ?array $filterValues = [];

    public ?array $defaultFilterValues = [];

    public array $searchFormData;

    public $beenFiltered = false;

    public $countFiltersUsed = 0;

    private $filtersUsed = [
        ['field' => 'keyword'],
        ['field' => 'year'],
        ['field' => 'view', 'skip' => true],
    ];

    public function boot()
    {
        $this->searchFormInit = new SpotifyMonthlyPlaylistSearchFormInit($this->searchFormData);
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
        $this->dispatch('spotify-monthly-playlist-searched', $this->filterValues);
    }

    public function render()
    {

        return view('livewire.spotify.spotify-monthly-playlist-search');
    }
}
