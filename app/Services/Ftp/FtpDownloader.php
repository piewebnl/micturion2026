<?php

namespace App\Services\Ftp;

use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FtpDownloader
{
    public function download(string $source, string $destination, string $channel, string $message = 'Download from FTP', Command $command = null)
    {

        $resource = [
            'source' => $source,
            'destination' => $destination,
        ];

        $fileContents = Storage::disk('ftp')->get($source);

        if ($fileContents) {
            file_put_contents(
                $destination,
                $fileContents
            );
            Logger::log(
                'notice',
                $channel,
                $message,
                $resource,
                $command
            );

            return;
        } else {
            Logger::log(
                'error',
                $channel,
                'Failed to ' . $message,
                $resource,
                $command
            );
        }
    }
}
