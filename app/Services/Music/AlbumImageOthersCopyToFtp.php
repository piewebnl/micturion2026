<?php

namespace App\Services\Music;

use App\Models\Music\Album;
use App\Services\Ftp\FtpUploader;
use App\Traits\Logger\Logger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlbumImageOthersUploadToFtp
{
    private string $channel = 'album_image_others_upload_to_ftp';

    public function copyAlbumImageOthersToFtp(int $id)
    {

        $album = Album::with('artist')->find($id);

        $slug = Str::slug($album->artist->name) . '/' . Str::slug($album->sort_name) . '/Folder.jpg';
        $source = config('music.album_artwork_others_path') . '/' . $slug;
        $destination = config('music.ftp_album_artwork_other_path') . '/' . $slug;

        if (!file_exists($source)) {
            Logger::log('warning', $this->channel, 'No album image others found  ' . $album->artist->name . ' - ' . $album->name);

            return;
        }

        // Get date modified
        $dateModifedLocal = filemtime($source);

        $disk = Storage::disk('ftp');
        if (!$disk->exists($destination)) {
            $dateModifiedRemote = null;
        } else {
            $dateModifiedRemote = $disk->lastModified($destination);
        }

        // echo $album->name;
        /*
        echo date("d M Y", $dateModifedLocal) . "\r\n";
        echo date("d M Y", $dateModifiedRemote) . "\r\n";
        */

        if ($dateModifiedRemote < $dateModifedLocal) {
            $ftpUploader = new FtpUploader;
            $ftpUploader->upload(
                $source,
                $destination,
                $this->channel,
                'Album other image copied to FTP: ' . $album->artist->name . ' - ' . $album->name,
            );

            return;
        }
        Logger::log(
            'info',
            $this->channel,
            'Album other image already exists on FTP: ' . $album->artist->name . ' - ' . $album->name,
            [
                'source' => $source,
                'destination' => $destination,

            ]
        );
    }
}
