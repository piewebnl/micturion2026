<?php

namespace App\Services\HtmlScraper;

use App\Traits\Logger\Logger;

class HtmlScraper
{
    private $url; // The URL to be scraped

    private $page; // The whole HTML Page

    private $section; // Page Section

    private $divider = '|||';

    private $channel = 'html_scraper';

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getSection()
    {
        return $this->section;
    }

    public function scrapeHTMLPage(?string $url, string $musicStoreKey): bool
    {
        if ($url == '') {
            $url = $this->getUrl();
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 10.10; labnol;) ctrlq.org');
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        // curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($curl);
        curl_close($curl);

        if ($html) {
            // echo 'scrape OK' . $musicStoreKey . "\r\n";
            $this->page = $html;
            if (!file_exists('./_scraped')) {
                mkdir('./_scraped', 0777, true);
            }
            file_put_contents('./_scraped/page-' . $musicStoreKey . '.txt', $html);

            Logger::log(
                'info',
                $this->channel,
                'HTML scraped: ' . $url,
                [$this->page]
            );

            return true;
        }

        Logger::log(
            'error',
            $this->channel,
            'HTML NOT scraped: ' . $url

        );

        // echo 'scrape NOT OK' . $musicStoreKey . "\r\n";
        /*
        if (!$html) {
            echo 'from cache';
            $html = file_get_contents('../page-' . $musicStoreKey . '.txt');
            $this->page = $html;
            return true;
        }
        */
        sleep(1);

        return false;
    }

    // Grab something (nested) between one or more start and ending tags

    // Like: <div class="grid--no-gutters grid--uniform grid">|||</div>
    //       <a href="||| class="product-card">
    // or: <span id="price_inside_buybox" class="a-size-medium a-color-price">|||</span>

    public function grabNested(string $searchTagString, ?string $sectionToSearch = null): string
    {

        if ($sectionToSearch == null) {
            $sectionToSearch = $this->page;
        }

        $searchTags = explode("\r\n", $searchTagString);

        // Lets keep it nice with the dashes
        $sectionToSearch = str_replace(
            ['–', '—', '−', '-', '‒', '⁃'],
            '-',
            $sectionToSearch
        );

        foreach ($searchTags as $tags) {

            $split = explode($this->divider, $tags);

            if (isset($split[0]) and isset($split[1])) {

                $start = $split[0];
                $end = $split[1];

                // echo 'START:' . $start . '<br />';
                // echo 'END:' . $end . '<br />';
                // echo $sectionToSearch;

                $found = $this->getStringBetween($sectionToSearch, $start, $end);

                $this->section = $found;
                $sectionToSearch = $found;
            }
        }

        return $this->section;
    }

    public function grabAll(string $searchTagString, ?string $sectionToSearch = null): array
    {
        $found = [];

        if ($sectionToSearch == null) {
            $sectionToSearch = $this->page;
        }

        $split = explode($this->divider, $searchTagString);

        if (isset($split[0]) and isset($split[1])) {

            $start = $split[0];
            $end = $split[1];

            // echo 'START:' . $start . '<br />';
            // echo 'END:' . $end . '<br />';

            // echo $sectionToSearch;
            $found = $this->getBetweenAll($sectionToSearch, $start, $end);
        }

        return $found;
    }

    private function getBetweenAll($content, $start, $end): array
    {

        $n = explode($start, $content);

        $result = [];

        foreach ($n as $val) {
            $pos = strpos($val, $end);
            if ($pos !== false) {
                $result[] = substr($val, 0, $pos);
            }
        }

        return $result;
    }

    private function getStringBetween($string, $start, $end)
    {

        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }

        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        $result = substr($string, $ini, $len);

        return $result;
    }
}
