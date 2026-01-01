<?php

use App\Http\Controllers\Api\Spotify\ApiSpotifyAlbumsController;
use App\Http\Controllers\Api\Spotify\ApiSpotifyAlbumsCustomIdsController;
use App\Http\Controllers\Api\Spotify\ApiSpotifyAlbumsUnavailableController;
use App\Http\Controllers\Api\Spotify\ApiSpotifyTracksController;
use App\Http\Controllers\Api\Spotify\ApiSpotifyTracksCustomIdsController;
use App\Http\Controllers\Api\Spotify\ApiSpotifyTracksUnavailableController;
use App\Http\Controllers\Spotify\SpotifyController;
use App\Http\Controllers\Spotify\SpotifyMonthlyPlaylistController;
use App\Http\Controllers\SpotifyApi\SpotifyApiAuthorizeCallbackController;
use App\Http\Controllers\SpotifyApi\SpotifyApiAuthorizeUrlController;
use App\Http\Controllers\SpotifyApi\SpotifyApiConnectController;
use App\Http\Controllers\SpotifyApi\SpotifyApiNowPlayingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->group(function () {

    Route::get('/spotify-api/authorize-callback', [SpotifyApiAuthorizeCallbackController::class, 'index'])->name('admin.spotify-api.authorize-callback'); // note! also set in .env
    Route::get('/spotify-api/authorize-url', [SpotifyApiAuthorizeUrlController::class, 'index'])->name('admin.spotify-api.authorize-url');
    // Route::get('/spotify-api/connect', [SpotifyApiConnectController::class, 'index'])->name('admin.spotify-api.connect');

    Route::get('spotify', [SpotifyController::class, 'index'])->name('admin.spotify');

    // BETER NAME?
    Route::get('/spotify/download-playlist/{filename}', function ($filename) {
        $path = storage_path("app/public/playlists/{$filename}");
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    })->name('spotify.download-playlist');
});

Route::group(['prefix' => 'api'], function () {

    Route::get('/spotify/tracks', [ApiSpotifyTracksController::class, 'index']);
    Route::get('/spotify/tracks/unavailable', [ApiSpotifyTracksUnavailableController::class, 'index']);
    Route::get('/spotify/tracks/custom-ids', [ApiSpotifyTracksCustomIdsController::class, 'index']);

    Route::get('/spotify/albums', [ApiSpotifyAlbumsController::class, 'index']);
    Route::get('/spotify/albums/unavailable', [ApiSpotifyAlbumsUnavailableController::class, 'index']);
    Route::get('/spotify/albums/custom-ids', [ApiSpotifyAlbumsCustomIdsController::class, 'index']);
});

Route::get('/spotify/monthly-playlists', [SpotifyMonthlyPlaylistController::class, 'index'])->name('spotify.monthly-playlists');
Route::get('/spotify/now-playing', [SpotifyApiNowPlayingController::class, 'index'])->name('spotify.now-playing');
