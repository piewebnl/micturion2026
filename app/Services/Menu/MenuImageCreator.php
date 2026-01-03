<?php

namespace App\Services\Menu;

use App\Models\Menu\Menu;
use App\Models\Menu\MenuImage;

// Create menu image (via upload or found on disk)
class MenuImageCreator
{
    public function createMenuImage(int $id)
    {

        $menu = Menu::find($id);

        $status = false;
        if ($menu) {

            $menuImageFinder = new MenuImageSourceFinder($menu);
            $sourceFilename = $menuImageFinder->findFilename();
            $menuImage = new MenuImage;
            $status = $menuImage->create($menu, $sourceFilename);
        }

        return $status;
    }
}
