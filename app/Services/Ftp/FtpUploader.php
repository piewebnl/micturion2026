<?php

namespace App\Services\Ftp;

use App\Traits\Logger\Logger;
use Illuminate\Support\Facades\Storage;

class FtpUploader
{
    private $response;

    public function upload(string $source, string $destination, string $channel, string $message = 'Copied to FTP')
    {

        $resource = [
            'source' => $source,
            'dest' => $destination,
        ];

        if (file_exists(($source))) {

            $fileContents = Storage::disk('ftp')->put($destination, file_get_contents($source));
            if ($fileContents) {

                Logger::log(
                    'notice',
                    $channel,
                    $message,
                    $resource
                );
            } else {

                Logger::log(
                    'error',
                    $channel,
                    'Failed to ' . $message,
                    $resource
                );
            }

            return;
        }

        Logger::log('warning', $channel, 'Source does not exist: ' . $source);
    }
}
