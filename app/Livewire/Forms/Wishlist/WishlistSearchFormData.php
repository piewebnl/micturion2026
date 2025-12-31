<?php

namespace App\Livewire\Forms\Wishlist;

use App\Models\Wishlist\MusicStore;
use App\Models\Wishlist\WishlistAlbum;
use App\Services\Forms\FormDataGenerator;
use App\Traits\QueryCache\QueryCache;

class WishlistSearchFormData
{
    use QueryCache;

    public function generate(): array
    {
        $formDataGenerator = new FormDataGenerator;

        // Wishlist albums
        $wishlistAlbums = new WishlistAlbum;
        $items = $wishlistAlbums->getAllWishlistAlbums([]);
        $formDataGenerator->setLabel(['artist_name', 'album_name']);
        $formDataGenerator->setValue('album_id');
        $formData['wishlist_albums'] = $formDataGenerator->generate($items);

        // Music stores
        $musicStores = new MusicStore;
        $items = $musicStores->getAllMusicStores([]);
        $formDataGenerator->setLabel(
            ['name']
        );
        $formDataGenerator->setMeta(
            ['url']
        );
        $formDataGenerator->setValue('id');
        $formData['music_stores'] = $formDataGenerator->generate($items);

        $formData['formats'] = [
            ['value' => null, 'label' => 'All'],
            ['value' => 'cd', 'label' => 'CD'],
            ['value' => 'lp', 'label' => 'LP'],
        ];

        // $formData['albums'] = $formDataGenerator->generate($items);
        $formData['sort'] = [
            ['value' => 'artist_sort_name', 'label' => 'Artist', 'order' => 'desc'],
            ['value' => 'album_year', 'label' => 'Album year', 'order' => 'asc'],
            ['value' => 'price', 'label' => 'Price', 'order' => 'asc'],
            ['value' => 'music_store_name', 'label' => 'Music store', 'order' => 'asc'],
        ];

        $formData['order_toggle_icon'] = 'up';

        return $formData;
    }
}
