<?php

namespace App\Services\Music;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SpineImageExtractor
{
    public string $filename;

    public string $destSlug;

    public function __construct(string $filename, $destSlug)
    {
        $this->filename = $filename;
        $this->destSlug = $destSlug;
    }

    public function extract(): string|false
    {
        if (!file_exists($this->filename)) {
            return false;
        }

        [$im, $w, $h, $ext] = $this->loadImage($this->filename);

        // Rotate portrait → landscape
        if ($h > $w) {
            $im = imagerotate($im, -90, 0);
            [$w, $h] = [imagesx($im), imagesy($im)];
        }

        // Fold detection (quick & light)
        $foldX = $this->detectFoldX($im, $w, $h);

        // Ratio enforcement
        $ratioWidth = $w / 27; // Spine = 1:25:1
        $cropW = (int) round($ratioWidth);

        // Slightly shift inward if a fold was found nearby
        if ($foldX && $foldX > ($w - $cropW * 1.5) && $foldX < ($w - $cropW * 0.5)) {
            $cropX = $foldX;
        } else {
            $cropX = $w - $cropW;
        }

        $spine = imagecrop($im, [
            'x' => max(0, $cropX),
            'y' => 0,
            'width' => $cropW,
            'height' => $h,
        ]);

        if (!$spine) {
            throw new RuntimeException("Failed to crop spine from {$this->filename}");
        }

        ob_start();
        imagejpeg($spine, null, 92);
        $data = ob_get_clean();

        $path = $this->destSlug . '.jpg';

        Storage::disk('spine_images_extracted')->put($path, $data);

        return Storage::url($path);
    }

    private function detectFoldX($im, int $w, int $h): ?int
    {
        // downsample
        $sw = 300;
        $scale = $sw / $w;
        $sh = (int) round($h * $scale);
        $small = imagecreatetruecolor($sw, $sh);
        imagecopyresampled($small, $im, 0, 0, 0, 0, $sw, $sh, $w, $h);

        // grayscale + horizontal edges
        $px = [];
        for ($y = 0; $y < $sh; $y++) {
            for ($x = 0; $x < $sw; $x++) {
                $rgb = imagecolorat($small, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $px[$y][$x] = (0.3 * $r + 0.59 * $g + 0.11 * $b);
            }
        }

        // vertical edge energy
        $colEnergy = array_fill(0, $sw, 0);
        for ($x = 1; $x < $sw - 1; $x++) {
            $sum = 0;
            for ($y = 0; $y < $sh; $y++) {
                $sum += abs($px[$y][$x + 1] - $px[$y][$x - 1]);
            }
            $colEnergy[$x] = $sum / $sh;
        }

        // Find peak near right edge
        $start = (int) round($sw * 0.7);
        $end = (int) round($sw * 0.95);
        $slice = array_slice($colEnergy, $start, $end - $start, true);
        if (!$slice) {
            return null;
        }

        arsort($slice);
        $bestX = array_key_first($slice);
        $confidence = reset($slice) / (array_sum($colEnergy) / count($colEnergy));

        imagedestroy($small);

        if ($confidence > 2.0) {
            // map back to original scale
            return (int) round($bestX / $scale);
        }

        return null;
    }

    private function loadImage(string $path): array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg'])) {
            $im = @imagecreatefromjpeg($path);
        } elseif ($ext === 'png') {
            $im = @imagecreatefrompng($path);
        } else {
            throw new RuntimeException("Unsupported extension: $ext");
        }
        if (!$im) {
            throw new RuntimeException("Cannot open image: $path");
        }

        return [$im, imagesx($im), imagesy($im), $ext];
    }
}
