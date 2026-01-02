<?php

namespace App\Console\Commands\Music;

use App\Models\Music\Album;
use App\Services\Music\SpineImageExtractor;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

// php artisan command:SpineImageExtractor
class SpineImageExtractorCommand extends Command
{
    protected $signature = 'command:SpineImageExtractor';

    private string $channel = 'spine_image_extractor';

    public function handle()
    {

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $albums = Album::with('artist', 'discogsReleases')->whereHas('discogsReleases')->get();

        if ($albums->isEmpty()) {
            Logger::log('error', $this->channel, 'No albums, or no matched or no discogs release info yet to extract spine images', [], $this);
            return;
        }

        $this->output->progressStart($albums->count());

        foreach ($albums as $album) {

            foreach ($album->discogsReleases as $discogsRelease) {


                if ($discogsRelease['format'] == 'CD') {

                    // Back artwork mostly 2nd pic
                    $imageLocation = storage_path('app/public/discogs-back-artwork/' . $discogsRelease['release_id'] . '-2.jpg');

                    $destSlug = Str::slug($album->artist->name . '-' . $album->sort_name) . '-' . $discogsRelease['release_id'];

                    $SpineImageExtractor = new SpineImageExtractor($imageLocation, $destSlug);
                    $SpineImageExtractor->extract();

                    $this->output->progressAdvance();
                }
            }
        }

        $this->output->progressFinish();
    }
}
