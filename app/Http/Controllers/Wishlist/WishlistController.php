<?php

namespace App\Http\Controllers\Wishlist;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Wishlist\WishlistSearchFormData;

class WishlistController extends Controller
{
    public function index()
    {

        $WishlistSearchFormData = new WishlistSearchFormData;
        $searchFormData = $WishlistSearchFormData->generate();

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'wishlist.wishlist-search',
                    'searchFormData' => $searchFormData,
                ],
                [
                    'name' => 'wishlist.wishlist-results',
                    'searchFormData' => $searchFormData,
                ],
            ],
        ]);
    }
}
