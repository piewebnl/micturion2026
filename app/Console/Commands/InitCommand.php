<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// php artisan command:Init
class InitCommand extends Command
{
    protected $signature = 'command:Init';

    private $channel = 'config';

    public function handle()
    {

        // Menu
        $this->call('command:MenuImageCreate');
    }
}
