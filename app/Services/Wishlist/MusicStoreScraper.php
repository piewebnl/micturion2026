<?php

namespace App\Services\Wishlist;

use App\Helpers\StringHelper;
use App\Models\Wishlist\MusicStore;
use App\Models\Wishlist\WishlistAlbum;
use App\Models\Wishlist\WishlistAlbumPrice;
use App\Services\HtmlScraper\HtmlScraper;
use App\Traits\Logger\Logger;
use Illuminate\Support\Str;

class MusicStoreScraper
{
    private $htmlScraper;

    private $musicStore;

    private $musicStoreConfig = [];

    private $wishlistAlbum;

    private $musicStoreSearchUrlSetup;

    private $musicStoreCreateProductUrl;

    private $foundItems = [];

    private $bestMatchedItem = [];

    private $configValues;

    private $resultsOrProduct = 'results';

    private $resource;

    private string $channel;

    public function __construct(MusicStore $musicStore, WishlistAlbum $wishlistAlbum)
    {
        $this->configValues = config('music-stores');
        $this->channel = 'wishlist_albums_scrape_prices';

        $this->htmlScraper = new HtmlScraper;
        $this->musicStoreSearchUrlSetup = new MusicStoreSearchUrlSetup;
        $this->musicStoreCreateProductUrl = new MusicStoreCreateProductUrl($this->htmlScraper);
        $this->wishlistAlbum = $wishlistAlbum;
        $this->musicStore = $musicStore;

        // Get config
        $key = array_search($this->musicStore['key'], array_column($this->configValues, 'key'));

        if ($key !== false) {
            $this->musicStoreConfig = $this->configValues[$key];
        }
    }

    public function scrape(string $format)
    {

        if (!$this->musicStoreConfig) {
            Logger::log('error', $this->channel, 'Something wrong with config for ' . $this->musicStore['name']);

            return;
        }

        if (!(isset($this->musicStoreConfig['search_url_' . $format]))) {

            return;
        }

        $searchUrl = $this->musicStoreSearchUrlSetup->setup($this->musicStoreConfig['search_url_' . $format], $this->wishlistAlbum['artist_name'], $this->wishlistAlbum['album_name']);

        $this->htmlScraper->scrapeHTMLPage($searchUrl, $this->musicStoreConfig['key']);
        $page = $this->htmlScraper->getPage();

        // Grab all items
        $scrapedItems = $this->htmlScraper->grabAll($this->musicStoreConfig['search_items']);

        if (empty($scrapedItems)) {
            return;
        }

        // Check product page (LARGE)
        if (count($scrapedItems) == 1) {

            if (Str::contains($page, '<meta property="og:type" content="product"')) {
                $this->resultsOrProduct = 'product';
                $scrapedItems[] = $page;
            }
        }

        $this->foundItems = [];

        foreach ($scrapedItems as $key => $item) {

            $this->foundItems[$key]['artist'] = $this->removeUnwantedStrings($this->htmlScraper->grabNested($this->musicStoreConfig[$this->resultsOrProduct]['search_item_artist'], $item));

            $this->foundItems[$key]['album'] = $this->removeUnwantedStrings($this->htmlScraper->grabNested($this->musicStoreConfig[$this->resultsOrProduct]['search_item_album'], $item));

            $this->foundItems[$key]['price'] = $this->removeUnwantedStrings($this->priceCorrection($this->htmlScraper->grabNested($this->musicStoreConfig[$this->resultsOrProduct]['search_item_price'], $item)));

            if (Str::startsWith($this->musicStoreConfig[$this->resultsOrProduct]['search_item_format'], 'FIXED:')) {
                $this->foundItems[$key]['format'] = trim(Str::after($this->musicStoreConfig[$this->resultsOrProduct]['search_item_format'], 'FIXED:'));
            } else {
                $this->foundItems[$key]['format'] = $this->htmlScraper->grabNested($this->musicStoreConfig[$this->resultsOrProduct]['search_item_format'], $item);
            }

            $this->foundItems[$key]['is_format_correct'] = $this->checkFormat($this->foundItems[$key]['format'], $format);

            $this->foundItems[$key]['page_url'] = $this->musicStoreCreateProductUrl->createProductUrl($this->musicStoreConfig, $this->resultsOrProduct, $item);
        }

        // print_r($this->foundItems);

        $this->removeWrongFormat();

        // Find best match
        $this->determineScore();

        // Sort
        usort($this->foundItems, [MusicStoreScraper::class, 'compareScore']);

        if (isset($this->foundItems[0])) {
            $this->bestMatchedItem = $this->foundItems[0];
        }

        // No price, make it 0
        if (!isset($this->bestMatchedItem['price'])) {
            $this->bestMatchedItem['price'] = 0;
        }

        if (!isset($this->bestMatchedItem['score'])) {
            $this->bestMatchedItem['score'] = 0;
        }

        if (!isset($this->bestMatchedItem['page_url'])) {
            $this->bestMatchedItem['page_url'] = null;
        }

        if (!isset($this->bestMatchedItem['format'])) {
            $this->bestMatchedItem['format'] = null;
        } else {
            $this->bestMatchedItem['format'] = $format;
        }

        // NAAR MODEL
        $wishlistAlbumPrice = new WishlistAlbumPrice;
        $result = $wishlistAlbumPrice->storeScrapeResult(
            [
                'wishlist_album_id' => $this->wishlistAlbum['wishlist_album_id'],
                'music_store_id' => $this->musicStore['id'],
                'price' => $this->bestMatchedItem['price'],
                'score' => $this->bestMatchedItem['score'],
                'format' => $this->bestMatchedItem['format'],
                'url' => $this->bestMatchedItem['page_url'],
            ]
        );
        Logger::log(
            'info',
            $this->channel,
            $this->musicStore->name . ' - ' . $this->wishlistAlbum['artist_name'] . ' ' . $this->wishlistAlbum['album_name'] . ' ' . $this->wishlistAlbum['price'] . ' scraped',
            $this->bestMatchedItem
        );

        // dd($this->bestMatchedItem);
    }

    private function removeWrongFormat()
    {
        foreach ($this->foundItems as $key => $item) {

            if (!$item['is_format_correct']) {
                unset($this->foundItems[$key]);
            }
        }
    }

    private function determineScore()
    {

        foreach ($this->foundItems as $key => $item) {

            $artistScore = StringHelper::similarName($this->wishlistAlbum['artist_name'], $item['artist']);

            $albumScore = StringHelper::similarName($this->wishlistAlbum['album_name'], $item['album']);

            $this->foundItems[$key]['score'] = intval(round(($artistScore + $albumScore) / 2));

            // echo $this->wishlistAlbum['album_name'] . ' ' . $item['album'] . "\r\n";
            // echo $this->foundItems[$key]['score'] . "\r\n" . "\r\n";;
        }
    }

    public static function compareScore($a, $b)
    {
        return $a['score'] < $b['score'];
    }

    private function checkFormat($foundFormat, $format): string
    {

        // 'search_item_format' => '', always pass format check
        if (
            $this->musicStoreConfig[$this->resultsOrProduct]['search_item_format'] == '' ||
            Str::startsWith($this->musicStoreConfig[$this->resultsOrProduct]['search_item_format'], 'FIXED:')
        ) {
            return true;
        }

        if (str_contains($foundFormat, $this->musicStoreConfig['format_name_' . $format])) {
            return true;
        }

        return false;
    }

    public function removeUnwantedStrings($name)
    {

        $name = strip_tags($name);

        // Remove all between ( ) and [ ]
        $name = preg_replace("/\([^)]+\)/", '   ', $name);
        $name = preg_replace("/\[[^)]+\]/", '', $name);

        $name = preg_replace("/\d{4}/", '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = ltrim($name, ' ');
        $name = rtrim($name, ' ');

        // Remove strange dashes
        $unwanted = ['Remastered Version', 'Remastered', 'Remaster', ' Edit', 'Coloured', 'Deluxe', 'Download', '-hq-', '=180gr=', '-coloured-', "Collector's Edition", '-ltd-'];

        foreach ($unwanted as $string) {
            $name = str_ireplace($string, '', $name);
        }

        $name = strtolower($name);

        return $name;
    }

    private function priceCorrection($foundPrice): string
    {

        $foundPrice = str_replace(',', '.', $foundPrice);
        $foundPrice = preg_replace('/[^0-9,.]/', '', $foundPrice);

        if ($this->musicStoreConfig['key'] == 'RECVINYL' & $foundPrice > 0) {
            return substr_replace($foundPrice, '.', -2, 0);
        }

        return $foundPrice;
    }

    public function getResource(): array
    {
        return $this->resource;
    }
}
