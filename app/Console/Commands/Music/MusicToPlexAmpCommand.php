<?php

namespace App\Console\Commands\Music;

use App\Helpers\VolumeMountedCheck;
use App\Services\Disk\MirrorDirectory;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:MusicToPlexAmp
class MusicToPlexAmpCommand extends Command
{
    protected $signature = 'command:MusicToPlexAmp';

    private string $channel = 'music_to_plex_amp';

    public function handle()
    {
        if (App::environment() != 'local') {
            return;
        }

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel)) {
            return;
        }

        Logger::deleteChannel($this->channel);

        $sourcePath = config('music.music_path');
        $destinationPath = config('music.plex_amp_path');

        if (!is_dir($sourcePath)) {
            Logger::log('error', $this->channel, "Source does not exist: {$sourcePath}");

            return;
        }

        // Ensure destination parent exists
        if (!is_dir(dirname($destinationPath))) {
            mkdir(dirname($destinationPath), 0777, true);
        }

        Logger::log('info', $this->channel, 'Copy to Plex Amp');
        // Logger::echo($this->channel);

        $excludePatterns = ['.DS_Store', '._*'];
        $mirror = new MirrorDirectory($sourcePath, $destinationPath, $excludePatterns);

        $success = $mirror->mirror();

        if ($success) {
            Logger::log('info', $this->channel, 'Mirror complete. Files processed: ' . $mirror->getProcessedFiles());
            $this->info('Mirror complete. ' . $mirror->getProcessedFiles() . ' files processed.');
        } else {
            Logger::log('error', $this->channel, 'Mirror failed to complete');
            $this->error('Mirror failed to complete');
        }

        // Logger::echo($this->channel);
    }
}
