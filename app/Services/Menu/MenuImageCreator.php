<?php

namespace App\Services\Menu;

use App\Models\Menu\Menu;
use App\Models\Menu\MenuImage;

// Create menu image (via upload or found on disk)
class MenuImageCreator
{
    private string $channel = 'menu_create_images';

    public function createMenuImage(int $id)
    {

        $menu = Menu::find($id);

        if ($menu) {

            $menuImageFinder = new MenuImageSourceFinder($menu);
            $sourceFilename = $menuImageFinder->findFilename();
            $menuImage = new MenuImage;
            $menuImage->create($menu, $sourceFilename);
        }
    }
}
