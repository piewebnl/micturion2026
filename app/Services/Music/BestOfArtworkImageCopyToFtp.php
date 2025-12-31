<?php

namespace App\Services\Music;

use App\Services\Ftp\FtpUploader;
use App\Traits\Logger\Logger;
use Illuminate\Support\Facades\Storage;

class BestOfArtworkImageCopyToFtp
{
    private $channel = 'best_of_artwork_image_copy_to_ftp';

    public function __construct() {}

    public function copyBestOfArtworkImageToFtp(string $playlistName)
    {

        $playlistName = basename($playlistName);

        $source = config('music.playlist_best_of_artwork_images_path') . '/' . $playlistName . '.jpeg';
        $destination = config('music.ftp_playlist_best_of_artwork_images_path') . '/' . $playlistName . '.jpeg';

        // Get date modified
        if (!file_exists($source)) {
            Logger::log(
                'error',
                $this->channel,
                'Best of artwork source does not exist: ' . $playlistName,
                [
                    'source' => $source,

                ]
            );

            return;
        }

        $dateModifedLocal = filemtime($source);

        $disk = Storage::disk('ftp');
        if (!$disk->exists($destination)) {
            $dateModifiedRemote = null;
        } else {
            $dateModifiedRemote = $disk->lastModified($destination);
        }

        // echo $album->name;
        // echo date("d M Y", $dateModifedLocal) . "\r\n";
        // echo date("d M Y", $dateModifiedRemote) . "\r\n";

        if ($dateModifiedRemote < $dateModifedLocal) {
            $ftpUploader = new FtpUploader;
            $ftpUploader->upload(
                $source,
                $destination,
                $this->channel,
                'Best of artwork copied to FTP: ' . $playlistName,
            );

            return;
        }
        Logger::log(
            'info',
            $this->channel,
            'Best of artwork already exists on FTP: ' . $playlistName,
            [
                'source' => $source,
                'destination' => $destination,

            ]
        );
    }
}
