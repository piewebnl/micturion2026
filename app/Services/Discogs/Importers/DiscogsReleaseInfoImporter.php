<?php

namespace App\Services\Discogs\Importers;

use App\Models\Discogs\DiscogsRelease;
use App\Services\DiscogsApi\Getters\DiscogsApiReleaseGetter;

// Get all extra release info from Discogs collection, loop and attach to discogs release
class DiscogsReleaseInfoImporter
{
    public function __construct(
        private DiscogsRelease $discogsRelease,
        private DiscogsApiReleaseGetter $releaseGetter = new DiscogsApiReleaseGetter
    ) {}

    public function import(): void
    {
        if (!$this->discogsRelease->release_id) {
            return;
        }

        $release = $this->releaseGetter->get($this->discogsRelease->release_id);

        if (!$release) {
            return;
        }

        // Image Urls
        $imageUrls = [];
        foreach ($release['images'] as $image) {
            $imageUrls[] = $image['uri'];
        }

        $this->discogsRelease->fill([
            'status' => 'imported',
            'notes' => $release['notes'] ?? null,
            'country' => $release['country'] ?? null,
            'date' => $release['released'] ?? null,
            'url' => $release['uri'] ?? null,
            'artwork_other_urls' => $imageUrls,
            'lowest_price' => $release['lowest_price'] ?? 0,
            'satus' => 'scraped',

        ]);

        $this->downloadReleaseImage();

        $this->discogsRelease->store($this->discogsRelease);
    }

    private function downloadReleaseImage()
    {

        $discogsBackArtworkPath = config('discogs.discogs_back_artwork_path');

        foreach ($this->discogsRelease['artwork_other_urls'] as $index => $artworkBackUrl) {
            if ($artworkBackUrl != '') {
                $imageContents = file_get_contents($artworkBackUrl);

                $localImagePath = storage_path($discogsBackArtworkPath . '/' . $this->discogsRelease['release_id'] . '-' . ($index + 1) . '.jpg');

                // Ensure directory exists
                $directory = dirname($localImagePath);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                file_put_contents($localImagePath, $imageContents);
            }
        }
    }
}
