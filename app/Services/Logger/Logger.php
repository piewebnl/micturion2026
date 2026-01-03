<?php

namespace App\Services\Logger;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Logger
{
    // Log to DB
    public static function log(string $type, string $channel, string $message, array $context = [], ?Command $command = null)
    {

        if ($type == 'info') {
            Log::channel($channel)->info($message, $context);
        }
        if ($type == 'notice') {
            Log::channel($channel)->notice($message, $context);
        }
        if ($type == 'warning') {
            Log::channel($channel)->warning($message, $context);
            if ($command) {
                $command->warn($message);
            }
        }
        if ($type == 'error') {
            Log::channel($channel)->error($message, $context);
            if ($command) {
                $command->error($message);
            }
        }
    }

    public static function displayName(string $channel)
    {
        return Str::title(str_replace('_', ' ', $channel));
    }

    // Echo to terminal or something
    public static function echoChannel(string $channel, ?Command $command = null)
    {

        if (App::environment() == 'local' && $command) {
            $name = self::displayName($channel);
            $command->info($name);
        }
    }

    public static function deleteChannel(string $channel)
    {

        File::delete(File::glob(storage_path('logs/' . $channel . '.log')));
    }
}
