<?php

namespace App\Livewire\LastFm;

use App\Livewire\Forms\LastFm\LastFmSearchFormInit;
use App\Models\Music\Album;
use App\Models\Music\Song;
use App\Traits\Forms\SearchForm;
use Livewire\Attributes\On;
use Livewire\Component;

class LastFmResults extends Component
{
    use SearchForm;

    private LastFmSearchFormInit $searchFormInit;

    public $filterValues = [];

    public $searchFormData;

    private $perPage = 50;

    public function boot()
    {
        $this->searchFormInit = new LastFmSearchFormInit($this->searchFormData);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
    }

    #[On('last-fm-searched')]
    public function searched($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    public function render()
    {
        $songIds = null;
        $album = null;
        $discCount = 1;

        // TO SERVICE?
        if ($this->filterValues['album']) {
            $all = Song::select([
                'id',
                'disc_count',
                'disc_number',
                'track_number',
                'name',
            ])->where('album_id', $this->filterValues['album'])
                ->orderBy('disc_number', 'asc')
                ->orderBy(
                    'track_number',
                    'asc'
                )->get();
            $discCount = Song::where('album_id', $this->filterValues['album'])->max('disc_count');

            $songIds['all'] = $all->toArray();

            for ($t = 0; $t < $discCount; $t++) {
                $songIds[$t + 1] = [];
            }

            // per disc
            foreach ($all as $song) {
                $songIds[$song['disc_number']][] = $song->toArray();
            }

            $album = Album::with('artist')->where('id', $this->filterValues['album'])->first();
        }

        return view('livewire.last-fm.last-fm-results', ['filterValues' => $this->filterValues, 'songIds' => $songIds, 'album' => $album, 'discCount' => $discCount]);
    }
}
