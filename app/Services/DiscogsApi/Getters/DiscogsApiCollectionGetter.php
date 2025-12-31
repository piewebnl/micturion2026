<?php

namespace App\Services\DiscogsApi\Getters;

use App\Traits\Logger\Logger;
use Illuminate\Support\Facades\Http;

class DiscogsApiCollectionGetter
{
    private $discogsApiCollectionReleases = [];

    private $page = 1;

    private $perPage = 50;

    private $lastPage = null;

    private $total = 0;

    private $channel = '';

    public function __construct(int $perPage)
    {
        $this->perPage = $perPage;
        $this->channel = 'discogs_collection_importer';
    }

    public function getPerPage(int $page): ?array
    {

        $this->page = $page;

        $url = config('discogs.discogs_api_url') . 'users/micturion/collection/folders/0/releases';

        $params = [
            'token' => config('discogs.discogs_personal_access_token'),
            'per_page' => $this->perPage,
            'page' => $this->page,
            'sort' => 'artist',
        ];

        // $response = Http::get($url, $params);

        if (// $response->successful()) {
            $data = // $response->json(); // Returns an associative array
        } else {
            Logger::log('error', $this->channel, 'Discogs API something went wrong');
            Logger::echo($this->channel);
        }

        if (!isset($data['releases'])) {
            Logger::log('error', $this->channel, 'Discogs API no releases found');
            Logger::echo($this->channel);
        } else {
            $this->discogsApiCollectionReleases = $data['releases'];
            $this->lastPage = // $response['pagination']['pages'];
            $this->total = count($this->discogsApiCollectionReleases);

            return $this->discogsApiCollectionReleases;
        }

        return null;
    }

    public function getLastPage(): ?int
    {
        $this->getPerPage(1);

        return $this->lastPage;
    }

    public function getTotal(): int
    {
        $this->getPerPage(1);

        return $this->total;
    }
}
