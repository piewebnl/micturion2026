<?php

namespace App\Services\Wishlist;

use App\Services\HtmlScraper\HtmlScraper;
use Illuminate\Support\Str;

class MusicStoreCreateProductUrl
{
    private $htmlScraper;

    public function __construct(HtmlScraper $htmlScraper)
    {
        $this->htmlScraper = $htmlScraper;
    }

    public function createProductUrl($musicStoreConfig, $resultsOrProduct, $item)
    {

        $slug = $this->htmlScraper->grabNested($musicStoreConfig[$resultsOrProduct]['search_item_page'], $item);

        if ($musicStoreConfig['key'] == 'RECVINYL') {
            $slug = str_replace('\/', '-', $slug);
            $slug = str_replace('.', '-', $slug);
            $slug = Str::slug($slug);
        }

        return $musicStoreConfig['product_page_url'] . $slug;
    }
}
