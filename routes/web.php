<?php

use App\Models\Menu\Menu;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;



Route::get('/', function () {
    return view('home');
});

Volt::route('/login', 'pages.auth.login')
    ->name('login');
