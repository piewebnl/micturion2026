<?php

namespace App\Console\Commands\Music;

use App\Models\Music\Album;
use App\Services\Music\SpineImageCreator;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;

// php artisan command:SpineImageCreate
class SpineImageCreateCommand extends Command
{
    protected $signature = 'command:SpineImageCreate';

    private string $channel = 'spine_image_create_images';

    public function handle()
    {

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        // Albums with Discogs Release and CD only
        $ids = Album::whereHas('discogsReleases', function ($q) {
            $q->where('format', 'cd');
        })
            ->orderBy('id', 'asc')
            ->pluck('id');

        if ($ids->isEmpty()) {
            Logger::log('error', $this->channel, 'No albums with discogs releases yet to create spine images', [], $this);

            return;
        }

        $this->output->progressStart(count($ids));

        foreach ($ids as $id) {
            $spineImageCreator = new SpineImageCreator;
            $spineImageCreator->createSpineImage($id);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
