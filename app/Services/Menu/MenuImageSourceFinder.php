<?php

namespace App\Services\Menu;

use App\Helpers\ImageHelper;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuImage;
use App\Traits\Logger\Logger;

// Find menu image on disk
class MenuImageSourceFinder
{
    private $menu;

    private $isFound = false;

    private $filename = '';

    private string $channel = 'menu_create_images';

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    public function isSourcefound(): bool
    {
        if ($this->isFound) {
            return true;
        }

        return false;
    }

    public function isSourceModified($hashFromDb): bool
    {
        $fileHash = ImageHelper::createHash($this->filename);
        if ($hashFromDb != $fileHash) {
            return true;
        }

        return false;
    }

    public function findFilename(): ?string
    {
        $menuImage = new MenuImage;
        $slug = $menuImage->getMenuImageSlug($this->menu);

        $this->filename = config('menus.menu_images_path') . '/' . $slug . '.jpg';

        if (file_exists($this->filename)) {
            $this->isFound = true;
        } else {
            Logger::log(
                'error',
                $this->channel,
                'Menu image source not found: ' . $this->menu->name,
                [
                    'filename' => $this->filename,
                ]
            );

            return null;
        }

        return $this->filename;
    }
}
