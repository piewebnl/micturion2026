<?php

namespace App\Console\Commands\Music;

use App\Helpers\VolumeMountedCheck;
use App\Models\Music\Album;
use App\Services\Music\AlbumImageOthersUploadToFtp;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:AlbumImageOthersUploadToFtp
class AlbumImageOthersUploadToFtpCommand extends Command
{
    protected $signature = 'command:AlbumImageOthersUploadToFtp';

    private string $channel = 'album_image_others_upload_to_ftp';

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

        $ids = Album::whereNull('location')->pluck('id')->toArray();

        if (!$ids) {
            Logger::log('error', $this->channel, 'No albums to copy', [], $this);

            return;
        }

        $this->output->progressStart(count($ids));

        foreach ($ids as $id) {
            $albumImageUploadToFtp = new AlbumImageOthersUploadToFtp;
            $albumImageUploadToFtp->uploadAlbumImageOthersToFtp($id, $this);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
