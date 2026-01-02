<?php

namespace App\Console\Commands\Music;

use App\Models\Music\Album;
use App\Services\Music\SpineImageCreator;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

// php artisan command:SpineImageCreate
class SpineImageCreateCommand extends Command
{
    protected $signature = 'command:SpineImageCreate';

    private string $channel = 'spine_image_create_images';

    public function handle()
    {

        Logger::deleteChannel($this->channel);

        // Albums with discogsrelease and CD only
        $albumIds = Album::whereHas('discogsReleases', function ($q) {
            $q->where('format', 'cd');
        })
            ->orderBy('id', 'asc')
            ->pluck('id');

        $this->output->progressStart(count($albumIds));

        foreach ($albumIds as $albumId) {
            $spineImageCreator = new SpineImageCreator;
            $spineImageCreator->createSpineImage($albumId);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        // Cache::flush();

        // Logger::echo($this->channel);
    }
}
