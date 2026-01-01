<?php

use App\Http\Controllers\Api\Concert\ApiConcertArtistController;
use App\Http\Controllers\Api\Concert\ApiConcertController;
use App\Http\Controllers\Api\Concert\ApiConcertFestivalController;
use App\Http\Controllers\Api\Concert\ApiConcertItemController;
use App\Http\Controllers\Api\Concert\ApiConcertVenueController;
use App\Http\Controllers\Concerts\AdminConcertController;
use App\Http\Controllers\Concerts\ConcertController;
use Illuminate\Support\Facades\Route;

Route::get('/concerts', [ConcertController::class, 'index'])->name('concerts.index');

/*
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/concerts', [AdminConcertController::class, 'index'])->name('admin.concerts');
    Route::get('/concerts/edit/{id}', [AdminConcertController::class, 'edit'])->name('admin.concerts.edit');
});
*/

Route::group(['prefix' => 'api'], function () {
    Route::get('/concerts', [ApiConcertController::class, 'index']);
    Route::get('/concert-artists', [ApiConcertArtistController::class, 'index']);
    Route::get('/concert-items', [ApiConcertItemController::class, 'index']);
    Route::get('/concert-venues', [ApiConcertVenueController::class, 'index']);
    Route::get('/concert-festivals', [ApiConcertFestivalController::class, 'index']);
});
