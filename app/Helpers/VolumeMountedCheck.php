<?php

namespace App\Helpers;

use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class VolumeMountedCheck
{
    public static function check($path, $channel, Command $command)
    {
        if (App::environment() == 'local' and !file_exists($path)) {
            Logger::deleteChannel($channel);
            Logger::log('error', $channel, $path . ' not mounted');
            $command->info($channel);
            $command->error($path . ' not mounted');

            return false;
        }

        return true;
    }
}
