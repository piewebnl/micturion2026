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

        // Handle images
        $imageUrls = [];
        foreach ($release['images'] as $image) {
            $imageUrls[] = $image['uri'];
        }

        $release['artwork_other_urls'] = $imageUrls;
        $this->downloadReleaseImage($imageUrls);
        $this->discogsRelease->storeFromDiscogsApiRelease($release);
    }

    private function downloadReleaseImage($imageUrls)
    {

        $discogsBackArtworkPath = config('discogs.discogs_back_artwork_path');

        foreach ($imageUrls as $index => $artworkBackUrl) {
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
