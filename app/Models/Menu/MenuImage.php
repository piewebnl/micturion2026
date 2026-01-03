<?php

namespace App\Models\Menu;

use App\Services\Images\ImageCreator;
use App\Services\Menu\MenuImageSourceFinder;
use App\Traits\Logger\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MenuImage extends Model
{
    protected $guarded = [];

    protected $type = 'menu';

    private string $channel = 'menu_create_images';

    private $slug;

    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }

    public function create(Menu $menu, $uploadFile = null)
    {
        $imageCreator = new ImageCreator($this->type);

        $this->menu = $menu;
        $this->resource = [
            'menu' => $this->menu,
        ];

        if ($uploadFile) {
            $source = $uploadFile;
        } else {
            $concertImageSourceFinder = new MenuImageSourceFinder($this->menu);
            $source = $concertImageSourceFinder->findFilename();
            if (!$source) {
                return;
            }
        }

        $this->slug = $this->getMenuImageSlug($this->menu);

        $create = false;
        if ($uploadFile) {
            $create = true;
        }
        if (!$uploadFile && $concertImageSourceFinder->isSourceModified($this->menu->concertImage?->hash)) {
            $create = true;
        }
        if (!$create and $this->existsInDb()) {
            Logger::log(
                'info',
                $this->channel,
                'Menu image not chagned: ' . $this->menu->name . ' [' . $this->menu->concert->date . ']'
            );

            return;
        }

        $imageCreator->create($source, $this->slug);
        $hash = $imageCreator->getHash();
        $largestWidth = $imageCreator->getLargestWidth();
        $largestHeight = $imageCreator->getLargestHeight();

        MenuImage::updateOrCreate(
            ['menu_id' => $this->menu->id],
            [
                'slug' => $this->slug,
                'largest_width' => $largestWidth,
                'largest_height' => $largestHeight,
                'hash' => $hash,
            ]
        );

        Logger::log(
            'info',
            $this->channel,
            'Menu image created: ' . $this->menu->name
        );

        return true;
    }

    public function existsInDb()
    {

        if ($this->menu->concertImage !== null) {
            return true;
        }

        return false;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getMenuImageSlug(Menu $menu): string
    {
        return Str::slug($menu->name . '-', '-');
    }
}
