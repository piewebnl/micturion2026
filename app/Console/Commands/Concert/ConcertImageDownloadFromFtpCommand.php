<?php

namespace App\Console\Commands\Concert;

use App\Helpers\VolumeMountedCheck;
use App\Services\Concert\ConcertImageDownloadFromFtp;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

// php artisan command:ConcertImageDownloadFromFtp
class ConcertImageDownloadFromFtpCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ConcertImageDownloadFromFtp';

    protected $description = 'Download concert images from FTP to local storage';

    private string $channel = 'concert_image_download_from_ftp';

    private array $remoteImages;

    public function handle()
    {

        if (!App::environment('local')) {
            return;
        }

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel, $this)) {
            return;
        }

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);


        $this->remoteImages = Storage::disk('ftp')->files(config('concerts')['ftp_concert_images_path']);

        $this->output->progressStart(count($this->remoteImages));

        foreach ($this->remoteImages as $remoteImage) {
            $concertImageDownloadFromFtp = new ConcertImageDownloadFromFtp;
            $concertImageDownloadFromFtp->copyConcertImageFromFtp($remoteImage);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
