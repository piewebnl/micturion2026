<?php

namespace App\Services\DiscogsApi\Getters;

use App\Services\Logger\Logger;
use Illuminate\Support\Facades\Http;

class DiscogsApiReleaseGetter
{
    private $config;

    private $channel = '';

    public function __construct()
    {
        $this->channel = 'discogs_release_info_importer';
    }

    public function get(int $releaseId): array
    {

        $url = config('discogs.discogs_api_url') . 'releases/' . $releaseId;

        $response = Http::get($url);

        if ($response->successful()) {
            $release = $response->json(); // Returns an associative array

        } else {
            $release = [];
            Logger::log('error', $this->channel, 'Discogs API something went wrong');
        }

        return $release;
    }
}
