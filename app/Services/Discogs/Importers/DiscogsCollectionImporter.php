<?php

namespace App\Services\Discogs\Importers;

use App\Models\Music\Album;
use App\Traits\Logger\Logger;
use App\Helpers\JsonHashHelper;
use Illuminate\Console\Command;
use App\Models\Discogs\DiscogsRelease;
use App\Models\Discogs\DiscogsReleaseCustomId;
use App\Models\DiscogsApi\DiscogsApiCollectionRelease;
use App\Services\DiscogsApi\Getters\DiscogsApiCollectionGetter;

class DiscogsCollectionImporter
{
    private DiscogsApiCollectionGetter $collectionGetter;

    private DiscogsApiCollectionRelease $collectionRelease;

    private array $releaseIds = [];

    private int $perPage = 50;

    private bool $hasToken = true;

    private string $channel = 'discogs_collection_importer';

    private Command $command;

    public function __construct(int $perPage, ?Command $command)
    {
        $this->perPage = $perPage;
        $this->command = $command;
        $this->collectionGetter = new DiscogsApiCollectionGetter($this->perPage);
        $this->collectionRelease = new DiscogsApiCollectionRelease;

        if (!config('discogs.discogs_personal_access_token')) {
            Logger::log('error', $this->channel, 'No personal access token found in config', [], $this->command);
            $this->hasToken = false;
        }
    }

    public function setReleaseIds(array $releaseIds): void
    {
        $this->releaseIds = $releaseIds;
    }

    public function importPage(int $page): array
    {
        if (!$this->hasToken) {
            return [];
        }

        $collectionReleases = collect($this->collectionGetter->getPerPage($page));
        $pageIds = [];

        foreach ($collectionReleases as $collectionRelease) {

            // Create a snapshot hash, to see if anything changed
            $collectionRelease['hash'] = JsonHashHelper::hash($collectionRelease);

            $pageIds[] = $collectionRelease['id'];

            // Only update if no hash exixts
            if (array_key_exists($collectionRelease['hash'], $this->releaseIds)) {
                Logger::log('info', $this->channel, 'Discogs collection release has not changed: ' . $collectionRelease['basic_information']['title'] . ' ' . $collectionRelease['id']);
            } else {
                $this->collectionRelease->storeFromDiscogApiCollectionRelease($collectionRelease);
                Logger::log('notice', $this->channel, 'Discogs collection release added/updated: ' . $collectionRelease['basic_information']['title'] . ' ' . $collectionRelease['id']);
            }
        }

        return $pageIds;
    }

    public function getLastPage(): ?int
    {
        if (!$this->hasToken) {
            return null;
        }

        return $this->collectionGetter->getLastPage();
    }

    public function finalizeImport(array $idsFromApi): void
    {
        if (!$this->hasToken) {
            return;
        }

        $this->logDuplicates($idsFromApi);
        $this->handleSkipped();
        $this->removeOld($idsFromApi);
    }

    private function logDuplicates(array $idsFromApi): void
    {
        $duplicates = array_unique(
            array_diff_assoc($idsFromApi, array_unique($idsFromApi))
        );

        foreach ($duplicates as $duplicate) {
            $artistAlbumName = $this->getArtistAlbumName((int) $duplicate);
            Logger::log(
                'error',
                $this->channel,
                'Duplicate relase on discogs.com: ' . $artistAlbumName . ' ' . $duplicate,
                [],
                $this->command
            );
        }
    }

    private function getArtistAlbumName(int $releaseId): ?string
    {
        $discogsRelease = DiscogsRelease::where('release_id', $releaseId)->first();

        if (!$discogsRelease) {
            return null;
        }

        $album = Album::with('artist')->where('id', $discogsRelease->album_id)->first();

        if ($album && $album->artist) {
            return $album->artist->name . ' - ' . $album->name;
        }

        if ($discogsRelease->artist || $discogsRelease->title) {
            return $discogsRelease->artist . ' - ' . $discogsRelease->title;
        }

        return null;
    }

    private function handleSkipped(): void
    {
        $skipped = DiscogsReleaseCustomId::where('release_id', 'skipped')->get();

        foreach ($skipped as $skip) {
            $album = Album::where('persistent_id', $skip['persistent_album_id'])->first();
            $discogsRelease = DiscogsRelease::where('album_id', $album->id)->first();

            if ($discogsRelease) {
                $discogsRelease->release_id = 0;
                $discogsRelease->score = 0;
                $discogsRelease->save();
            }
        }
    }

    private function removeOld(array $idsFromApi): void
    {
        if (!$idsFromApi) {
            return;
        }

        $diff = array_diff($this->releaseIds, $idsFromApi);
        DiscogsRelease::whereIn('release_id', $diff)->delete();
        Logger::log(
            'warning',
            $this->channel,
            'Deleted skipped: ' . count($diff),
            [],
            $this->command
        );
    }
}
