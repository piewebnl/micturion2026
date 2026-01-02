<?php

namespace App\Console\Commands\Concert;

use App\Helpers\VolumeMountedCheck;
use App\Models\Concert\ConcertItem;
use App\Services\Concert\ConcertImageCreator;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;

// php artisan command:ConcertImageCreate
class ConcertImageCreateCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ConcertImageCreate';

    private string $channel = 'concert_create_images';

    public function handle()
    {

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel, $this)) {
            return;
        }

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $ids = ConcertItem::with(['Concert', 'ConcertArtist'])->orderBy('id', 'asc')->pluck('id');


        if ($ids->isEmpty()) {
            Logger::log('error', $this->channel, 'No concert items found', [], $this);
            return;
        }

        $this->output->progressStart(count($ids));

        foreach ($ids as $id) {
            $concertImageCreator = new ConcertImageCreator;
            $concertImageCreator->createConcertImage($id);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
