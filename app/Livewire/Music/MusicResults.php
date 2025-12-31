<?php

namespace App\Livewire\Music;

use App\Livewire\Forms\Music\MusicSearchFormInit;
use App\Models\Music\Artist;
use App\Models\Wishlist\WishlistAlbum;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class MusicResults extends Component
{
    private MusicSearchFormInit $musicSearchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public $filterValues = [];

    public $searchFormData;

    public $perPage = 100;

    public function boot()
    {
        $this->musicSearchFormInit = new MusicSearchFormInit($this->searchFormData);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->musicSearchFormInit->init($this->filterValues);
    }

    #[On('music-searched')]
    public function searched($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    // MOVE TO WISHLIST?
    public function addToWishlist($albumPersistentId)
    {
        $result = WishlistAlbum::where('persistent_album_id', $albumPersistentId)->first();
        if (isset($result->id)) {
            return;
        }

        $wishlistAlbum = new WishlistAlbum;
        $wishlistAlbum->persistent_album_id = $albumPersistentId;
        $wishlistAlbum->save();
    }

    public function loadMore()
    {
        $this->filterValues['per_page'] += $this->perPage;
    }

    public function render()
    {

        $artist = new Artist;

        if ($this->filterValues['view'] == 'spines') {
            $this->filterValues['formats'] = [9];
        }

        $loadedArtists = $artist->getArtistsWithAlbums($this->filterValues);
        $artists = $loadedArtists->toArray()['data'];

        return view(
            'livewire.music.music-results',
            [
                'artists' => $artists,
                'loadedArtists' => $loadedArtists,
            ]
        );
    }
}
