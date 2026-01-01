<?php

use App\Http\Controllers\Api\Discogs\ApiDiscogsReleaseCustomIdsController;
use App\Http\Controllers\Discogs\DiscogsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/discogs/', [DiscogsController::class, 'index'])->name('admin.discogs.index');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('/discogs/release-custom-ids', [ApiDiscogsReleaseCustomIdsController::class, 'index']);
});
