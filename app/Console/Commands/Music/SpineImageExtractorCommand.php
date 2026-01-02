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

    private string $channel;

    public function handle()
    {
        $this->channel = 'spine_image_extractor';

        Logger::deleteChannel($this->channel);

        if (App::environment() == 'local') {
            Logger::echoChannel($this->channel);
        }

        $albums = Album::with('artist', 'discogsReleases')->whereHas('discogsReleases')->get();

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

        // Logger::echo($this->channel);
    }
}
