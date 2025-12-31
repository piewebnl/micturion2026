<?php

namespace App\Providers;

use App\Models\Menu\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $menu = new Menu;
            $menus = $menu->getAllMenus();
            $view->with('menus', $menus); // cached
        });
    }
}
