<?php

namespace App\Services\DiscogsApi\Getters;

use Illuminate\Support\Facades\Http;

class DiscogsApiStatsGetter
{
    public function get(int $releaseId): array
    {
        $url = config('discogs.discogs_api_url') . 'releases/' . $releaseId . '/stats';

        $response = Http::get($url);

        if ($response->successful()) {
            $release = $response->json(); // Returns an associative array
        } else {
            dd($response);
        }

        return $release;
    }
}
