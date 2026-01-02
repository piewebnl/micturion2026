<?php

namespace App\Services\Ftp;

use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FtpDownloader
{
    public function download(string $source, string $destination, string $channel, string $message = 'Download from FTP', ?Command $command = null)
    {

        $resource = [
            'source' => $source,
            'destination' => $destination,
        ];

        $remoteModified = Storage::disk('ftp')->lastModified($source);

        if (file_exists($destination)) {
            $localModified = filemtime($destination);
            if ($localModified !== false && $remoteModified !== false && $remoteModified <= $localModified) {
                Logger::log(
                    'info',
                    $channel,
                    'Skipped (up-to-date): ' . basename($source),
                    $resource,
                    $command
                );

                return;
            }
        }

        $fileContents = Storage::disk('ftp')->get($source);

        if ($fileContents) {
            $dir = dirname($destination);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
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
