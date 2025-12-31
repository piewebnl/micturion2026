<?php

namespace App\Traits\Logger;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Logger
{
    // Log to DB
    public static function log(string $type, string $channel, string $message, array $context = [])
    {

        $channelFilename = self::changeName($channel);

        if ($type == 'info') {
            Log::channel($channelFilename)->info($message, $context);
        }
        if ($type == 'notice') {
            Log::channel($channelFilename)->notice($message, $context);
        }
        if ($type == 'warning') {
            Log::channel($channelFilename)->warning($message, $context);
        }
        if ($type == 'error') {
            Log::channel($channelFilename)->error($message, $context);
        }
    }

    public static function echoChannel(string $channel)
    {
        echo Str::title(str_replace('_', ' ', $channel)) . "\r\n\r\n";
    }

    // Echo to terminal or something
    public static function echo(string $channel)
    {
        $isCli = PHP_SAPI === 'cli';

        if (App::environment() == 'local') {
            echo "\r\n";
            $channelFilename = self::changeName($channel);
            $logFile = storage_path() . '/logs/' . $channelFilename . '.log';

            echo basename($logFile) . "\r\n\r\n";
            if (file_exists($logFile)) {
                $logFile = file($logFile);
                foreach ($logFile as $line) {
                    echo $isCli ? $line : htmlspecialchars($line);
                }
            }

            echo "\r\n\r\n";
        }
    }

    public static function deleteChannel(string $channel)
    {
        $channelFilename = self::changeName($channel);
        File::delete(File::glob(storage_path('logs/' . $channelFilename . '.log')));
    }

    private static function changeName($channel)
    {
        $channel = strtolower($channel);
        $channel = Str::snake($channel);

        return $channel;
    }
}
