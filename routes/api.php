<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Wishlist\ApiMusicStoresController;
use App\Http\Controllers\Api\Wishlist\ApiWishlistAlbumsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/wishlist/wishlist-albums', [ApiWishlistAlbumsController::class, 'index']);
    Route::get('/wishlist/music-stores', [ApiMusicStoresController::class, 'index']);
    Route::post('/wishlist/wishlist-albums-prices', [ApiWishlistAlbumsController::class, 'store']);
});
