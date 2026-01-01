<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('home');
});

Volt::route('/login', 'pages.auth.login')
    ->name('login');


Route::middleware('auth')->group(function () {

    Route::post('/admin/flush-cache', function () {
        Artisan::call('cache:clear');
        session()->flash('success', 'Cache flushed successfully!');

        return redirect()->back();
    })->name('flush.cache');
});
