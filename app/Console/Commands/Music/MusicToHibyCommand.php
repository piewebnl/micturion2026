<?php

namespace App\Console\Commands\Music;

use App\Helpers\VolumeMountedCheck;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToRetrieveMetadata;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

// php artisan command:MusicToHiby
class MusicToHibyCommand extends Command
{
    protected $signature = 'command:MusicToHiby';

    protected $description = '';

    private string $channel = 'music_to_hiby';

    private array $skipExact = ['.DS_Store', 'Thumbs.db'];

    private array $skipPrefix = ['._'];

    public function handle()
    {

        set_time_limit(0);

        $local = rtrim(env('PATH_TO_MUSIC'), '/');
        $disk = Storage::disk('hiby');

        if (App::environment() !== 'local') {
            return 0;
        }

        if (!VolumeMountedCheck::check('/Volumes/iTunes', $this->channel)) {
            return;
        }

        Logger::deleteChannel($this->channel);

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($local, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $uploaded = 0;
        $skipped = 0;
        $errors = 0;
        $count = 0;
        $limit = 0;
        $echoEvery = 100;

        foreach ($it as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $base = $file->getBasename();
            if ($this->shouldSkip($base)) {
                continue;
            }

            if ($limit > 0 && $count >= $limit) {
                break;
            }
            $count++;

            // Remote path
            $rel = ltrim(str_replace($local, '', $file->getPathname()), '/');
            $remotePath = str_replace(DIRECTORY_SEPARATOR, '/', $rel);
            $dir = dirname($remotePath);

            // Ensure remote directory exists (recursive create, no existence check)
            if ($dir !== '.' && $dir !== '/' && $dir !== '') {
                $this->ensureRemoteDirectory($disk, $dir);
            }

            $localSize = (int) $file->getSize();
            $remoteSize = $this->safeRemoteSize($disk, $remotePath);

            if ($remoteSize !== $localSize) {
                try {
                    $this->putStreamWithRetry($disk, $remotePath, $file->getPathname(), 3);
                    Logger::log('info', $this->channel, "Copied: {$remotePath}");
                    $uploaded++;
                } catch (\Throwable $e) {
                    Logger::log('error', $this->channel, "Error: {$remotePath} - " . $e->getMessage());
                    $errors++;
                }
            } else {
                Logger::log('info', $this->channel, "Skipping: {$remotePath}");
                $skipped++;
            }

            if ($count % $echoEvery === 0) {
                Logger::echo($this->channel);
            }
        }

        Logger::echo($this->channel);

        $this->mirrorDelete($disk, $local);

        Logger::log('info', $this->channel, "Uploaded: {$uploaded}, Skipped: {$skipped}, Errors: {$errors}");
        Logger::echo($this->channel);

        return $errors > 0 ? 2 : 0;
    }

    private function ensureRemoteDirectory($disk, string $dir): void
    {
        $dir = trim($dir, '/');
        if ($dir === '') {
            return;
        }

        $segments = array_values(array_filter(explode('/', $dir), 'strlen'));
        $path = '';

        foreach ($segments as $seg) {
            $path = ($path === '') ? $seg : ($path . '/' . $seg);
            try {
                $disk->createDirectory($path); // no-op if it exists on most adapters
            } catch (\Throwable $e) {
                // Ignore "already exists" / chdir warnings from some FTP servers
                // Re-throw only on clear hard failures
                $msg = strtolower($e->getMessage());
                if (!(str_contains($msg, 'exists') || str_contains($msg, 'file already'))) {
                    // brief backoff then one more try
                    usleep(200000);
                    try {
                        $disk->createDirectory($path);
                    } catch (\Throwable $e2) { /* swallow */
                    }
                }
            }
        }
    }

    private function safeRemoteSize($disk, string $remotePath): int
    {
        try {
            return (int) $disk->fileSize($remotePath);
        } catch (UnableToRetrieveMetadata $e) {
            return -1;
        } catch (\Throwable $e) {
            Logger::log('warning', $this->channel, "fileSize warn: {$remotePath} - " . $e->getMessage());

            return -1;
        }
    }

    /**
     * Stream upload with small retry loop.
     * Storage::put($path, $resource) streams via Flysystem (no buffering).
     */
    private function putStreamWithRetry($disk, string $remotePath, string $localPath, int $tries = 3): void
    {
        $attempt = 0;
        while (true) {
            $attempt++;

            $stream = @fopen($localPath, 'rb');
            if ($stream === false) {
                throw new \RuntimeException("Open failed: {$localPath}");
            }

            try {
                $disk->put($remotePath, $stream);

                return;
            } catch (\Throwable $e) {
                if ($attempt >= $tries) {
                    throw $e;
                }
                usleep(300000); // 300 ms
            } finally {
                if (is_resource($stream)) {
                    @fclose($stream);
                }
            }
        }
    }

    private function mirrorDelete($disk, string $local): void
    {
        $localSet = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($local, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($it as $file) {
            if ($file->isFile() && !$this->shouldSkip($file->getBasename())) {
                $rel = ltrim(str_replace($local, '', $file->getPathname()), '/');
                $localSet[str_replace(DIRECTORY_SEPARATOR, '/', $rel)] = true;
            }
        }

        foreach ($disk->allFiles('') as $remoteFile) {
            $base = basename($remoteFile);
            if ($this->shouldSkip($base)) {
                continue;
            }

            if (!isset($localSet[$remoteFile])) {
                try {
                    $disk->delete($remoteFile);
                    $this->warn("[DEL]   {$remoteFile}");
                } catch (\Throwable $e) {
                    $this->error("[FAIL]  delete {$remoteFile} :: " . $e->getMessage());
                }
            }
        }
    }

    private function shouldSkip(string $name): bool
    {
        if (in_array($name, $this->skipExact, true)) {
            return true;
        }
        foreach ($this->skipPrefix as $pref) {
            if (str_starts_with($name, $pref)) {
                return true;
            }
        }

        return false;
    }
}
