<?php

namespace App\Console\Commands\Music;

use App\Helpers\VolumeMountedCheck;
use App\Models\Music\Album;
use App\Services\Music\AlbumImageUploadToFtp;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:AlbumImageUploadToFtp
class AlbumImageUploadToFtpCommand extends Command
{
    protected $signature = 'command:AlbumImageUploadToFtp';

    private string $channel = 'album_image_upload_to_ftp';

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

        $ids = Album::whereNotNull('location')->pluck('id')->toArray();

        if (!$ids) {
            Logger::log('info', $this->channel, 'No albums to copy');
            return;
        }

        $this->output->progressStart(count($ids));

        foreach ($ids as $id) {
            $albumImageUploadToFtp = new AlbumImageUploadToFtp;
            $albumImageUploadToFtp->uploadAlbumImagetoFtp($id, $this);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
