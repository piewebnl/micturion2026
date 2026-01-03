<?php

namespace App\Services\Music;

use App\Services\Ftp\FtpUploader;

class BestOfArtworkImageUploadToFtp
{
    private $channel = 'best_of_artwork_image_upload_to_ftp';

    public function uploadBestOfArtworkImageToFtp(string $playlistName)
    {

        $playlistName = basename($playlistName);

        $source = config('music.playlist_best_of_artwork_images_path') . '/' . $playlistName . '.jpeg';
        $destination = config('music.ftp_playlist_best_of_artwork_images_path') . '/' . $playlistName . '.jpeg';

        $ftpUploader = new FtpUploader;
        $ftpUploader->upload(
            $source,
            $destination,
            $this->channel,
            'Best of artwork copied to FTP: ' . $playlistName,
        );
    }
}
