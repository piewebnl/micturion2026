<?php

namespace App\Console\Commands\Music;

use App\Helpers\VolumeMountedCheck;
use App\Models\Playlist\Playlist;
use App\Services\Music\BestOfArtworkImageUploadToFtp;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:BestOfArtworkImageUploadToFtp
class BestOfArtworkImageUploadToFtpCommand extends Command
{
    protected $signature = 'command:BestOfArtworkImageUploadToFtp';

    private string $channel = 'best_of_artwork_image_upload_to_ftp';

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

        $playlists = Playlist::where('parent_name', 'Best Of')->get();

        if (count($playlists) == 0) {
            Logger::log('error', $this->channel, 'No playlists to copy');

            return;
        }

        $this->output->progressStart(count($playlists));

        foreach ($playlists as $playlist) {

            $bestOfArtworkImageUploadToFtp = new BestOfArtworkImageUploadToFtp;
            $bestOfArtworkImageUploadToFtp->uploadBestOfArtworkImageToFtp($playlist->name);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
