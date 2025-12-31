<?php

namespace App\Services\Music;

use App\Models\Music\Album;
use App\Services\Ftp\FtpUploader;
use App\Traits\Logger\Logger;
use Illuminate\Support\Facades\Storage;

class AlbumImageCopyToFtp
{
    private string $channel = 'album_image_copy_to_ftp';

    public function copyAlbumImagetoFtp(int $id)
    {

        $album = Album::find($id);

        if (!$album->location) {
            Logger::log('warning', $this->channel, 'No location (: ' . $album->location);

            return;
        }

        $source = config('music.music_path') . $album->location . 'Folder.jpg';
        $destination = config('music.ftp_music_path') . $album->location . 'Folder.jpg';

        // Get date modified
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
                'Album image copied to FTP: ' . $album->artist->name . ' - ' . $album->name,
            );

            return;
        }
        Logger::log(
            'info',
            $this->channel,
            'Album image already exists on FTP: ' . $album->artist->name . ' - ' . $album->name,
            [
                'source' => $source,
                'destination' => $destination,

            ]
        );
    }
}
