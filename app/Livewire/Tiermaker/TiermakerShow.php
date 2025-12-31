<?php

namespace App\Livewire\Tiermaker;

use App\Models\Music\Artist;
use App\Models\Tiermaker\TiermakerArtist;
use Livewire\Component;

class TiermakerShow extends Component
{
    public $id;

    public $artistName;

    public $tiermakerArtists;

    public array $labels = [];

    public function boot()
    {

        $this->labels = config('tiermaker.labels');

        $this->artistName = Artist::where('id', $this->id)->value('name');
    }

    public function render()
    {
        $this->tiermakerArtists = TiermakerArtist::with('tiermakerAlbums.album.albumImage')
            ->with('artist')
            ->orderBy(
                Artist::select('name')
                    ->whereColumn('artists.name', 'tiermaker_artists.artist_name')
                    ->limit(1)
            )
            ->where('artist_name', $this->artistName)
            ->customPaginateOrLimit([]);

        return view('livewire.tiermaker.tiermaker-show', [
            'tiermakerArtists' => $this->tiermakerArtists,
            'labels' => $this->labels,
        ]);
    }
}
