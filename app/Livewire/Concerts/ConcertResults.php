<?php

namespace App\Livewire\Concerts;

use App\Livewire\Forms\Concerts\ConcertSearchFormInit;
use App\Models\Concert\Concert;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ConcertResults extends Component
{
    private ConcertSearchFormInit $concertSearchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public $filterValues = [];

    public $concertSearchFormData;

    public $perPage = 100;

    public function boot()
    {
        $this->concertSearchFormInit = new ConcertSearchFormInit($this->concertSearchFormData);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->concertSearchFormInit->init($this->filterValues);
    }

    #[On('concerts-searched')]
    public function searched($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    public function loadMore()
    {
        $this->filterValues['per_page'] += $this->perPage;
    }

    public function render()
    {

        $concert = new Concert;
        $loadedConcerts = $concert->getConcerts($this->filterValues);

        return view('livewire.concerts.concert-results', [
            'concerts' => $loadedConcerts->toArray()['data'],
            'loadedConcerts' => $loadedConcerts,
        ]);
    }
}
