<?php

namespace App\Console\Commands\Concert;

use App\Helpers\VolumeMountedCheck;
use App\Models\Concert\ConcertFestival;
use App\Services\Concert\ConcertFestivalImageCreator;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;

// php artisan command:ConcertFestivalImageCreate
class ConcertFestivalImageCreateCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ConcertFestivalImageCreate';

    protected $description = 'Creates concerts festival images';

    private string $channel = 'concert_festival_create_images';

    public function handle()
    {

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel, $this)) {
            return;
        }

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $concertFestivalIds = ConcertFestival::with(['ConcertItem', 'Concert', 'ConcertArtist'])->orderBy('id', 'asc')->pluck('id');

        if (!$concertFestivalIds) {
            Logger::log('error', $this->channel, 'No concert festivals found');

            return;
        }

        $this->output->progressStart(count($concertFestivalIds));

        foreach ($concertFestivalIds as $concertFestivalId) {
            $concertImageCreator = new ConcertFestivalImageCreator;
            $concertImageCreator->createConcertFestivalImage($concertFestivalId);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
