<?php

namespace App\Console\Commands\Music;

use App\Helpers\VolumeMountedCheck;
use App\Models\Playlist\Playlist;
use App\Services\Music\BestOfArtworkImageCopyToFtp;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:BestOfArtworkImageCopyToFtp
class BestOfArtworkImageCopyToFtpCommand extends Command
{
    protected $signature = 'command:BestOfArtworkImageCopyToFtp';

    private string $channel = 'best_of_artwork_image_copy_to_ftp';

    public function handle()
    {
        if (App::environment() != 'local') {
            return;
        }

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel)) {
            return;
        }

        Logger::deleteChannel($this->channel);

        $playlists = Playlist::where('parent_name', 'Best Of')->get();

        Logger::echoChannel($this->channel);

        if (count($playlists) > 0) {

            $this->output->progressStart(count($playlists));

            foreach ($playlists as $playlist) {

                $bestOfArtworkImageCopyToFtp = new BestOfArtworkImageCopyToFtp;
                $bestOfArtworkImageCopyToFtp->copyBestOfArtworkImageToFtp($playlist->name);
                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
        } else {

            Logger::log('info', $this->channel, 'Nothing to copy');
        }

        // Logger::echo($this->channel);
    }
}
