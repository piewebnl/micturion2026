<?php

use App\Http\Controllers\Api\Music\ApiSongByTimeController;
use App\Http\Controllers\Api\Music\ApiSongWithAlbumController;
use App\Http\Controllers\Music\AlbumRandomController;
use App\Http\Controllers\Music\AlbumWithoutDiscogsController;
use App\Http\Controllers\Music\MusicController;
use App\Http\Controllers\Music\MusicStatsController;
use App\Http\Controllers\Tiermaker\TiermakerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('music/stats', [MusicStatsController::class, 'index'])->name('music.stats');
    Route::get('music/albums-without-discogs', [AlbumWithoutDiscogsController::class, 'index'])->name('music.album-without-discogs');

    Route::get('/tiermaker', [TiermakerController::class, 'index'])->name('tiermaker.index');
    Route::get('/tiermaker/create/{id}', [TiermakerController::class, 'create'])->name('tiermaker.create');
});

Route::get('/music', [MusicController::class, 'index'])->name('music');
Route::get('/music/albums/random', [AlbumRandomController::class, 'index'])->name('music.albums.random');

Route::group(['prefix' => 'api'], function () {
    Route::get('/music/get-song-by-time/{track_number}/{time}', [ApiSongByTimeController::class, 'index'])->name('music.get-song-by-time');
    Route::get('/music/get-songs-with-album/{album_id}', [ApiSongWithAlbumController::class, 'index'])->name('music.album');
});
