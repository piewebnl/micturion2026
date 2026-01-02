<?php

namespace App\Console\Commands\Concert;

use App\Helpers\VolumeMountedCheck;
use App\Services\Concert\ConcertImageCopyFromFtp;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

// php artisan command:ConcertImageCopyFromFtp
class ConcertImageCopyFromFtpCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ConcertImageCopyFromFtp';

    private string $channel = 'concert_image_copy_from_ftp';

    private array $remoteImages;

    public function handle()
    {

        if (App::environment() != 'local') {
            return;
        }

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel)) {
            return;
        }

        Logger::deleteChannel($this->channel);

        $this->remoteImages = Storage::disk('ftp')->files(config('concerts')['ftp_concert_images_path']);

        $this->output->progressStart(count($this->remoteImages));

        foreach ($this->remoteImages as $remoteImage) {
            $concertImageDownloadFromFtp = new ConcertImageCopyFromFtp;
            $concertImageDownloadFromFtp->copyConcertImageFromFtp($remoteImage);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        // Logger::echo($this->channel);
    }
}
