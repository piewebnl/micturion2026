<?php

namespace App\Livewire\Spotify;

use App\Livewire\Forms\Spotify\SpotifyMonthlyPlaylistSearchFormInit;
use App\Models\Spotify\SpotifyPlaylist;
use App\Traits\QueryCache\QueryCache;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class SpotifyMonthlyPlaylistResults extends Component
{
    use QueryCache;

    private SpotifyMonthlyPlaylistSearchFormInit $searchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public $filterValues = [];

    public $searchFormData;

    private $perPage = 100;

    public function boot()
    {
        $this->searchFormInit = new SpotifyMonthlyPlaylistSearchFormInit($this->searchFormData);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
    }

    #[On('spotify-monthly-playlist-searched')]
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
        $spotifyPlaylist = new SpotifyPlaylist;
        $playlists = $spotifyPlaylist->getSpotifyPlaylistsWithTracks($this->filterValues);

        return view('livewire.spotify.spotify-monthly-playlist-results', compact('playlists'));
    }
}
