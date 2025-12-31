<?php

use App\Models\Menu\Menu;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;



Route::get('/', function () {
    $menu = new Menu;
    $menus = $menu->getAllMenus();
    return view('home', compact('menus'));
});

Volt::route('/login', 'pages.auth.login')
    ->name('login');
