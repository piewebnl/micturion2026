<?php

namespace App\Console\Commands\Concert;

use App\Helpers\VolumeMountedCheck;
use App\Services\Ftp\FtpDownloader;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

// php artisan command:ConcertFestivalImageCopyFromFtp
class ConcertFestivalImageCopyFromFtpCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ConcertFestivalImageCopyFromFtp';

    private string $channel = 'concert_festival_image_import';

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
        Logger::echoChannel($this->channel);

        $this->remoteImages = Storage::disk('ftp')->files(config('concerts')['ftp_concert_festival_images_path']);

        $this->output->progressStart(count($this->remoteImages));

        $ftpDownloader = new FtpDownloader;

        foreach ($this->remoteImages as $remoteImage) {
            $dest = config('concerts')['concert_festival_images_path'] . '/' . basename($remoteImage);
            $ftpDownloader->download($remoteImage, $dest, $this->channel);
            $this->output->progressAdvance();
        }

        $this->clearCache('concerts');

        $this->output->progressFinish();
        Logger::echo($this->channel);
    }
}
