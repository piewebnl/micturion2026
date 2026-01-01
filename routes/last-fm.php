<?php

use App\Http\Controllers\LastFm\LastFmScrobbleController;
use App\Http\Controllers\LastFmApi\LastFmApiAuthorizeCallbackController;
use App\Http\Controllers\LastFmApi\LastFmApiAuthorizeController;
use App\Http\Controllers\LastFmApi\LastFmScrobbleTrackController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->group(function () {

    Route::get('/last-fm-api/authorize', [LastFmApiAuthorizeController::class, 'index'])->name('admin.last-fm-api.authorize');
    Route::get('/last-fm-api/authorize-callback', [LastFmApiAuthorizeCallbackController::class, 'index'])->name('admin.last-fm-api.authorize-callback');
    Route::get('/last-fm-api/scrobble', [LastFmScrobbleController::class, 'index'])->name('admin.last-fm-api.scrobble');
    Route::post('/last-fm-api/scrobble/track/create', [LastFmScrobbleTrackController::class, 'store'])->name('admin.last-fm-api.scrobble.track.create');
});
