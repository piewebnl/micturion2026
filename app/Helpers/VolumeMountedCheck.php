<?php

namespace App\Helpers;

use App\Traits\Logger\Logger;
use Illuminate\Support\Facades\App;

class VolumeMountedCheck
{
    public static function check($path, $channel)
    {
        if (App::environment() == 'local' and !file_exists($path)) {
            Logger::deleteChannel($channel);
            Logger::log('error', $channel, $path . ' not mounted');
            Logger::echo($channel);

            return false;
        }

        return true;
    }
}
