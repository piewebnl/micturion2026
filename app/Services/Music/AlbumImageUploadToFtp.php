<?php

namespace App\Services\Music;

use App\Models\Music\Album;
use App\Services\Ftp\FtpUploader;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;

class AlbumImageUploadToFtp
{
    private string $channel = 'album_image_upload_to_ftp';

    public function uploadAlbumImagetoFtp(int $id, ?Command $command = null)
    {

        $album = Album::find($id);

        if (!$album->location) {
            Logger::log('warning', $this->channel, 'No location (: ' . $album->location, [], $command);

            return;
        }

        $source = config('music.music_path') . $album->location . 'Folder.jpg';
        $destination = config('music.ftp_music_path') . $album->location . 'Folder.jpg';

        $ftpUploader = new FtpUploader;
        $ftpUploader->upload(
            $source,
            $destination,
            $this->channel,
            'Album image copied to FTP: ' . $album->artist->name . ' - ' . $album->name,
        );
    }
}
