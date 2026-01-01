<?php

use App\Http\Controllers\Api\Wishlist\ApiMusicStoresController;
use App\Http\Controllers\Api\Wishlist\ApiWishlistAlbumsController;
use App\Http\Controllers\Wishlist\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->group(function () {

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('admin.wishlist');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('/wishlist/wishlist-albums', [ApiWishlistAlbumsController::class, 'index']);
    Route::get('/wishlist/music-stores', [ApiMusicStoresController::class, 'index']);
});
