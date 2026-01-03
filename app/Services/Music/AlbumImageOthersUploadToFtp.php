<?php

namespace App\Services\Music;

use App\Models\Music\Album;
use App\Services\Ftp\FtpUploader;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AlbumImageOthersUploadToFtp
{
    private string $channel = 'album_image_others_upload_to_ftp';

    public function uploadAlbumImageOthersToFtp(int $id, ?Command $command)
    {

        $album = Album::with('artist')->find($id);

        $slug = Str::slug($album->artist->name) . '/' . Str::slug($album->sort_name) . '/Folder.jpg';
        $source = config('music.album_artwork_others_path') . '/' . $slug;
        $destination = config('music.ftp_album_artwork_other_path') . '/' . $slug;

        if (!file_exists($source)) {
            Logger::log('warning', $this->channel, 'No album image others found  ' . $album->artist->name . ' - ' . $album->name, [], $command);

            return;
        }

        $ftpUploader = new FtpUploader;
        $ftpUploader->upload(
            $source,
            $destination,
            $this->channel,
            'Album other image copied to FTP: ' . $album->artist->name . ' - ' . $album->name,
        );
    }
}
