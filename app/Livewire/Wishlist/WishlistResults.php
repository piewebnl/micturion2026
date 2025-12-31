<?php

namespace App\Livewire\Wishlist;

use App\Livewire\Forms\Wishlist\WishlistSearchFormInit;
use App\Models\Wishlist\WishlistAlbum;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class WishlistResults extends Component
{
    private WishlistSearchFormInit $wishlistSearchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public $filterValues = [];

    public $wishlistSearchFormData;

    public $perPage = 50;

    public function boot()
    {
        $this->wishlistSearchFormInit = new WishlistSearchFormInit($this->wishlistSearchFormData);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->wishlistSearchFormInit->init($this->filterValues);
    }

    #[On('wishlist-searched')]
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

        $wishlistAlbum = new WishlistAlbum;

        $loadedWishlistAlbums = $wishlistAlbum->getWishlistAlbumsWithPrices($this->filterValues);
        $wishlistAlbums = $loadedWishlistAlbums->toArray()['data'];

        if ($this->filterValues['sort'] == 'artist_sort_name' || $this->filterValues['sort'] == 'album_year') {
            return view('livewire.wishlist.wishlist-results-by-album', ['wishlistAlbums' => $wishlistAlbums, 'loadedWishlistAlbums' => $loadedWishlistAlbums]);
        }
        if ($this->filterValues['sort'] == 'price') {
            return view('livewire.wishlist.wishlist-results-by-price', ['wishlistAlbums' => $wishlistAlbums, 'loadedWishlistAlbums' => $loadedWishlistAlbums]);
        }
        if ($this->filterValues['sort'] == 'music_store_name') {
            return view('livewire.wishlist.wishlist-results-by-music-store', ['wishlistAlbums' => $wishlistAlbums, 'loadedWishlistAlbums' => $loadedWishlistAlbums]);
        }
    }
}
