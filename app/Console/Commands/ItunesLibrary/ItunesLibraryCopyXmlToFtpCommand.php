<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

// php artisan command:ItunesLibraryCopyXmlToFtp
class ItunesLibraryCopyXmlToFtpCommand extends Command
{
    protected $signature = 'command:ItunesLibraryCopyXmlToFtp';

    private string $channel = 'itunes_library_xml_copy_to_ftp';

    private string $dest;

    private string $source;

    public function handle()
    {

        if (App::environment() != 'local') {
            return;
        }

        Logger::deleteChannel($this->channel);

        $this->setup();

        if (file_exists(($this->source))) {
            $this->copyToFtp();
        } else {
            Logger::log('error', $this->channel, 'iTunes library XML failed: ' . $this->source . ' not found');
        }

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }

    private function setup()
    {
        $this->source = storage_path() . config('ituneslibrary.itunes_library_xml_file');
        $this->dest = config('ituneslibrary.ftp_itunes_library_xml_file');
    }

    private function copyToFtp()
    {
        $fileContents = Storage::disk('ftp')->put($this->dest, file_get_contents($this->source));

        $this->output->progressStart(1);

        if ($fileContents) {
            Logger::log('info', $this->channel, 'iTunes library XML copied to FTP: ' . $this->dest);
            $this->output->progressAdvance();
        } else {
            Logger::log('error', $this->channel, 'iTunes library XML failed to copy to FTP: ' . $this->dest);
        }
    }
}
