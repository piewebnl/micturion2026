<?php

namespace App\Services\Discogs\Importers;

use App\Helpers\JsonHashHelper;
use App\Models\Discogs\DiscogsRelease;
use App\Models\DiscogsApi\DiscogsApiCollectionRelease;
use App\Services\DiscogsApi\Getters\DiscogsApiCollectionGetter;
use App\Services\DiscogsApi\Getters\DiscogsApiReleaseGetter;
use App\Traits\Logger\Logger;

class DiscogsCollectionImporter
{
    private $collectionReleases;

    private DiscogsApiCollectionGetter $collectionGetter;

    private DiscogsApiCollectionRelease $collectionRelease;

    private $ids = []; // Ids from the api

    private $releaseIds;

    public $perPage = 50;

    public $releaseGetter;

    public $total;

    public $lastPage;

    public function __construct(int $perPage)
    {
        $this->perPage = $perPage;
        $this->collectionGetter = new DiscogsApiCollectionGetter($perPage);
        $this->releaseGetter = new DiscogsApiReleaseGetter;
        $this->collectionRelease = new DiscogsApiCollectionRelease;

        $this->releaseIds = DiscogsRelease::pluck('release_id', 'hash')->toArray();

        if (!config('discogs.discogs_personal_access_token')) {
            Logger::log('error', 'discogs_collection_importer', 'No personal access token found in config');
            Logger::echo('discogs_collection_importer');

            return;
        }
    }

    public function import(int $page): void
    {
        $this->collectionReleases = collect($this->collectionGetter->getPerPage($page));
        $this->total = $this->collectionGetter->getTotal();

        foreach ($this->collectionReleases as $collectionRelease) {

            // Create a snapshot hash, to see if anything changed
            $collectionRelease['hash'] = JsonHashHelper::hash($collectionRelease);

            $this->ids[] = $collectionRelease['id'];

            // Only update if no hash exixts
            if (array_key_exists($collectionRelease['hash'], $this->releaseIds)) {
                Logger::log('info', 'discogs_collection_importer', 'Discogs collection release has not changed: ' . $collectionRelease['basic_information']['title'] . ' ' . $collectionRelease['id']);
            } else {
                $this->collectionRelease->storeFromDiscogApiCollectionRelease($collectionRelease);
                Logger::log('notice', 'discogs_collection_importer', 'Discogs collection release added/updated: ' . $collectionRelease['basic_information']['title'] . ' ' . $collectionRelease['id']);
            }
        }
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function getLastPage(): ?int
    {
        return $this->lastPage = $this->collectionGetter->getLastPage();
    }
}
