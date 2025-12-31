<?php

namespace App\Services\Concert;

use App\Services\Ftp\FtpDownloader;

class ConcertImageCopyFromFtp
{
    private string $channel = 'concert_image_copy_from_ftp';

    public function copyConcertImageFromFtp(string $source)
    {

        $destination = config('concerts.concert_images_path') . '/' . basename($source);

        $ftpUploader = new FtpDownloader;
        $ftpUploader->download(
            $source,
            $destination,
            $this->channel,
            'Concert image downloaded from FTP to disk: ' . basename($source),
        );
    }
}
