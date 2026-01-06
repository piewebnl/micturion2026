<?php

namespace App\Console\Commands\Music;

use App\Helpers\VolumeMountedCheck;
use App\Models\Music\Album;
use App\Services\Logger\Logger;
use App\Services\Music\AlbumImageCreator;
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

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel, $this)) {
            return;
        }

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $ids = Album::with(['Album', 'AlbumArtist'])->orderBy('id', 'asc')->pluck('id');

        if (!$ids) {
            Logger::log('error', $this->channel, 'No album items found', [], $this);

            return;
        }

        $clearCache = false;

        $this->output->progressStart(count($ids));

        foreach ($ids as $id) {
            $albumImageCreator = new AlbumImageCreator;
            $status = $albumImageCreator->createAlbumImage($id);
            if ($status) {
                $clearCache = true;
            }
            $this->output->progressAdvance();
        }

        if ($clearCache) {
            $this->clearCache('music', $this->channel, $this);
        }

        $this->output->progressFinish();
    }
}
