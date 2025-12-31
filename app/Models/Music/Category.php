<?php

namespace App\Models\Music;

use App\Models\ItunesLibrary\ItunesLibraryTrack;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use QueryCache;

    protected $guarded = [];

    public function storeItunesLibraryTrack(ItunesLibraryTrack $itunesTrack)
    {
        if ($itunesTrack->category != null) {
            $category = Category::firstOrNew(['name' => $itunesTrack->category]);
            $category->save();

            return $category->id;
        }
    }

    public function getCategoriesByName(array $names)
    {
        $ids = [];
        $categories = Category::all()->toArray();

        foreach ($names as $name) {
            $key = array_search($name, array_column($categories, 'name'));
            $ids[] = $categories[$key]['id'];
        }

        return $ids;
    }

    public function getCategoryByGrouping(?string $grouping): ?array
    {
        $categories = Category::all()->toArray();

        if (!$categories) {
            exit('no categories. Please seed');
        }

        $foundCat = [];

        foreach ($categories as $cat) {
            if (strpos(strtoupper($grouping), strtoupper($cat['format_match'])) !== false) {
                $foundCat = $cat;
            }
        }

        return $foundCat;
    }

    public function getAllCategories()
    {

        $categories = $this->getCache('get-all-categories');

        if (!$categories) {
            $categories = Category::groupBy('name')->where('name', '<>', 'Songs')->orderBy('order')->get();
            $this->setCache('get-all-categories', [], $categories);
        }

        return $categories;
    }
}
