<?php

namespace App\Services\Disk;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class MirrorDirectory
{
    private int $totalFiles = 0;

    private int $processedFiles = 0;

    private array $excludePatterns = [];

    public function __construct(
        private string $sourcePath,

        private string $destinationPath,

        array $excludePatterns = ['.DS_Store', '._*']
    ) {
        $this->excludePatterns = $excludePatterns;
    }

    public function mirror(): bool
    {
        if (!is_dir($this->sourcePath)) {
            return false;
        }

        // First pass: count total files
        $this->countFiles();

        // Second pass: copy files
        return $this->copyFiles();
    }

    private function countFiles(): void
    {
        $this->totalFiles = 0;
        $this->processedFiles = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($this->shouldExclude($file)) {
                continue;
            }
            $this->totalFiles++;
        }
    }

    /**
     * Copy files recursively with progress reporting
     */
    private function copyFiles(): bool
    {
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($this->shouldExclude($file)) {
                    continue;
                }

                $relativePath = substr($file->getRealPath(), strlen($this->sourcePath) + 1);
                $destFile = $this->destinationPath . DIRECTORY_SEPARATOR . $relativePath;

                if ($file->isDir()) {
                    if (!is_dir($destFile)) {
                        mkdir($destFile, 0777, true);
                    }
                } else {
                    $destDir = dirname($destFile);
                    if (!is_dir($destDir)) {
                        mkdir($destDir, 0777, true);
                    }

                    if ($this->fileNeedsUpdate($file->getRealPath(), $destFile)) {
                        copy($file->getRealPath(), $destFile);
                    }
                    $this->processedFiles++;
                    $this->reportProgress();
                }
            }

            // Clean up destination files that don't exist in source
            $this->removeExtraFiles();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove files from destination that don't exist in source
     */
    private function removeExtraFiles(): void
    {
        if (!is_dir($this->destinationPath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->destinationPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            $relativePath = substr($file->getRealPath(), strlen($this->destinationPath) + 1);
            $sourceFile = $this->sourcePath . DIRECTORY_SEPARATOR . $relativePath;

            if (!file_exists($sourceFile)) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
        }
    }

    /**
     * Check if file should be excluded
     */
    private function shouldExclude(SplFileInfo $file): bool
    {
        $filename = $file->getFilename();

        foreach ($this->excludePatterns as $pattern) {
            if ($this->matchPattern($filename, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if file needs to be copied based on size and modification time
     */
    private function fileNeedsUpdate(string $sourceFile, string $destFile): bool
    {
        if (!file_exists($destFile)) {
            return true;
        }

        $sourceSize = filesize($sourceFile);
        $destSize = filesize($destFile);
        $sourceMtime = filemtime($sourceFile);
        $destMtime = filemtime($destFile);

        // Copy if sizes differ or source is newer
        return $sourceSize !== $destSize || $sourceMtime > $destMtime;
    }

    /**
     * Match filename against glob pattern
     */
    private function matchPattern(string $filename, string $pattern): bool
    {
        $regex = str_replace(
            ['*', '?'],
            ['.*', '.'],
            preg_quote($pattern, '#')
        );

        return preg_match("#^{$regex}$#", $filename) === 1;
    }

    private function reportProgress(): void
    {
        $percentage = $this->totalFiles > 0 ? floor(($this->processedFiles / $this->totalFiles) * 100) : 0;

        echo sprintf(
            "\r%3d%% [%d/%d]",
            $percentage,
            $this->processedFiles,
            $this->totalFiles
        );

        if ($this->processedFiles === $this->totalFiles) {
            echo "\n";
        }
    }

    public function getProcessedFiles(): int
    {
        return $this->processedFiles;
    }

    }
