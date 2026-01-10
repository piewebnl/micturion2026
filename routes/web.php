<?php

use Livewire\Volt\Volt;
use App\Models\Music\Album;
use Illuminate\Support\Facades\Route;
use App\Models\Wishlist\WishlistAlbum;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('home');
});



Volt::route('/login', 'pages.auth.login')
    ->name('login');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/admin/flush-cache', function () {
        Artisan::call('cache:clear');
        session()->flash('success', 'Cache flushed successfully!');

        return redirect()->back();
    })->name('flush.cache');
});
