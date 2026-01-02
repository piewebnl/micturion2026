<?php

namespace App\Console\Commands\Music;

use App\Helpers\VolumeMountedCheck;
use App\Models\Music\Album;
use App\Services\Music\AlbumImageCreator;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;

// php artisan command:AlbumImageCreate
class AlbumImageCreateCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:AlbumImageCreate';

    private string $channel = 'album_create_images';

    public function handle()
    {

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel)) {
            return;
        }

        Logger::deleteChannel($this->channel);

        $ids = Album::with(['Album', 'AlbumArtist'])->orderBy('id', 'asc')->pluck('id');

        if (!$ids) {
            Logger::log('error', $this->channel, 'No album items found');

            return;
        }

        $this->output->progressStart(count($ids));

        foreach ($ids as $id) {
            $albumImageCreator = new AlbumImageCreator;
            $albumImageCreator->createAlbumImage($id);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
