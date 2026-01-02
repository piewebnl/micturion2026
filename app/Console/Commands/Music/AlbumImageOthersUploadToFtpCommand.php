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
        if (App::environment() != 'local') {
            return;
        }

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel)) {
            return;
        }

        Logger::deleteChannel($this->channel);

        $ids = Album::whereNull('location')->pluck('id')->toArray();

        Logger::echoChannel($this->channel);

        if (count($ids) > 0) {

            $this->output->progressStart(count($ids));

            foreach ($ids as $id) {
                $albumImageUploadToFtp = new AlbumImageOthersUploadToFtp;
                $albumImageUploadToFtp->copyAlbumImageOthersToFtp($id);
                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
        } else {

            Logger::log('info', $this->channel, 'No albums to copy');
        }

        // Logger::echo($this->channel);
    }
}
