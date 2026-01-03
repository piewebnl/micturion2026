<?php

namespace App\Livewire\Tiermaker;

use App\Livewire\Forms\Tiermaker\TiermakerSearchFormInit;
use App\Models\Music\Artist;
use App\Models\Tiermaker\TiermakerArtist;
use App\Traits\Forms\SearchForm;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class TiermakerResults extends Component
{
    use SearchForm;

    private TiermakerSearchFormInit $searchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public $filterValues = [];

    public $searchFormData;

    public $labels;

    public $tiers;

    private $perPage = 30;

    public function boot()
    {
        $this->searchFormInit = new TiermakerSearchFormInit($this->searchFormData);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
        $this->labels = config('tiermaker.labels');
    }

    public function loadMore()
    {
        $this->filterValues['per_page'] += $this->perPage;
    }

    #[On('tiermaker-searched')]
    public function searched($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    public function render()
    {

        $filterValues = $this->filterValues;

        $tiermakerArtists = TiermakerArtist::with('tiermakerAlbums.album.albumImage')
            ->with('artist')
            ->orderBy(
                Artist::select('name')
                    ->whereColumn('artists.name', 'tiermaker_artists.artist_name')
                    ->limit(1)
            )
            ->customPaginateOrLimit($filterValues);

        return view('livewire.tiermaker.tiermaker-results', [
            'filterValues' => $this->filterValues,
            'tiermakerArtists' => $tiermakerArtists,
            'labels' => $this->labels,
        ]);
    }
}
