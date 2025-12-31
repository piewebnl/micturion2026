<?php

namespace App\Services\Music;

use getID3;

class AlbumArtworkExtractor
{
    private array $allowedExt = ['mp3', 'm4a', 'aac'];

    public function extractToFolderJpg(string $folderPath): ?string
    {
        $folderPath = rtrim($folderPath, '/');

        if (!is_dir($folderPath)) {
            return null;
        }

        $audioFile = $this->findFirstAudioFile($folderPath);
        if (!$audioFile) {
            return null;
        }

        $temp = tempnam(sys_get_temp_dir(), 'getid3_');
        if ($temp === false) {
            return null;
        }

        $fpSource = null;
        $fpTemp = null;

        try {
            $fpSource = fopen($audioFile, 'rb');
            if (!is_resource($fpSource)) {
                return null;
            }

            $fpTemp = fopen($temp, 'wb');
            if (!is_resource($fpTemp)) {
                return null;
            }

            stream_copy_to_stream($fpSource, $fpTemp);

            $info = (new getID3)->analyze($temp);

            $picture = $info['comments']['picture'][0] ?? null;

            if (!$picture || empty($picture['data'])) {
                return null;
            }

            $image = imagecreatefromstring($picture['data']);
            if ($image === false) {
                return null;
            }

            $dest = $folderPath . '/Folder.jpg';
            imagejpeg($image, $dest, 90);

            return $dest;
        } finally {
            if (is_resource($fpTemp)) {
                fclose($fpTemp);
            }
            if (is_resource($fpSource)) {
                fclose($fpSource);
            }
            if (file_exists($temp)) {
                unlink($temp);
            }
        }
    }

    private function findFirstAudioFile(string $folderPath): ?string
    {
        $items = scandir($folderPath);
        if ($items === false) {
            return null;
        }

        foreach ($items as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (str_starts_with($file, '._')) {
                continue;
            }

            $fullPath = $folderPath . '/' . $file;

            if (!is_file($fullPath)) {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $this->allowedExt, true)) {
                continue;
            }

            return $fullPath;
        }

        return null;
    }
}
