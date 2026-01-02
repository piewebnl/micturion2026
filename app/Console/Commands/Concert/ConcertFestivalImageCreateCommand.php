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

    private string $channel = 'concert_festival_create_images';

    public function handle()
    {

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel)) {
            return;
        }

        Logger::deleteChannel($this->channel);

        $ids = ConcertFestival::with(['ConcertItem', 'Concert', 'ConcertArtist'])->orderBy('id', 'asc')->pluck('id');

        if (!$ids) {
            Logger::log('error', $this->channel, 'No concert festivals found');

            return;
        }

        $this->output->progressStart(count($ids));

        foreach ($ids as $id) {
            $concertImageCreator = new ConcertFestivalImageCreator;
            $concertImageCreator->createConcertFestivalImage($id);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
