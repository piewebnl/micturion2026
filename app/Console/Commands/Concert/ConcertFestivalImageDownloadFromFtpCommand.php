<?php

namespace App\Console\Commands\Concert;

use App\Helpers\VolumeMountedCheck;
use App\Services\Ftp\FtpDownloader;
use App\Services\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

// php artisan command:ConcertFestivalImageDownloadFromFtp
class ConcertFestivalImageDownloadFromFtpCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ConcertFestivalImageDownloadFromFtp';

    protected $description = 'Download concert festival images from FTP to iTunes volume';

    private string $channel = 'concert_festival_image_download_from_ftp';

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

        $remoteImages = Storage::disk('ftp')->files(config('concerts.ftp_concert_festival_images_path'));

        if (empty($remoteImages)) {
            Logger::log('warning', $this->channel, 'No concert festivals found', [], $this);

            return;
        }

        $this->output->progressStart(count($remoteImages));

        $ftpDownloader = new FtpDownloader;

        foreach ($remoteImages as $remoteImage) {
            $dest = config('concerts.concert_festival_images_path') . '/' . basename($remoteImage);
            $ftpDownloader->download($remoteImage, $dest, $this->channel, 'Downloaded from FTP: ' . basename($remoteImage), $this);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
