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
        if (App::environment() != 'local') {
            return;
        }

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel)) {
            return;
        }

        Logger::deleteChannel($this->channel);

        $ids = Album::whereNotNull('location')->pluck('id')->toArray();

        Logger::echoChannel($this->channel);

        if (count($ids) > 0) {

            $this->output->progressStart(count($ids));

            foreach ($ids as $id) {
                $albumImageUploadToFtp = new AlbumImageUploadToFtp;
                $albumImageUploadToFtp->copyAlbumImagetoFtp($id);
                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
        } else {

            Logger::log('info', $this->channel, 'No albums to copy');
        }

        // Logger::echo($this->channel);
    }
}
