<?php

namespace App\Models\Menu;

use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Menu extends Model
{
    use QueryCache;

    protected $guarded = [];

    public function menuImage()
    {
        return $this->belongsTo(MenuImage::class, 'id', 'menu_id');
    }

    public function storeOrUpdate(Menu $menu): Menu
    {
        return Menu::UpdateOrCreate(
            ['id' => $menu->id],
            [
                'name' => $menu->name,
                'order' => $menu->order,

            ]
        );
    }

    public function storeImages(Menu $menu, array $images = [])
    {
        foreach ($images as $image) {
            $menuImage = new MenuImage;
            $menuImage->create($menu, $image->getPath() . '/' . $image->getFilename());
        }
    }

    public function getAllMenus()
    {

        $menus = $this->getCache('get-all-menus');

        if (!$menus) {

            // check schema
            if (!Schema::hasTable('menus')) {
                // die('No menu table');
                return;
            }

            $menus = Menu::with('menuImage')->orderBy('order')->get();
            $this->setCache('get-all-menus', [], $menus);
        }

        return $menus;
    }
}
