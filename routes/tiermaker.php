<?php

use App\Http\Controllers\Api\Tiermaker\ApiTiermakerAlbumsController;
use App\Http\Controllers\Api\Tiermaker\ApiTiermakerArtistsController;
use App\Http\Controllers\Tiermaker\TiermakerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/tiermaker', [TiermakerController::class, 'index'])->name('tiermaker.index');
    Route::get('/tiermaker/{id}', [TiermakerController::class, 'show'])->name('tiermaker.show');
    Route::get('/tiermaker/edit/{id}', [TiermakerController::class, 'edit'])->name('tiermaker.edit');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('/tiermaker/artists', [ApiTiermakerArtistsController::class, 'index']);
    Route::get('/tiermaker/albums', [ApiTiermakerAlbumsController::class, 'index']);
});
