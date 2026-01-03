<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\TiermakerAlbumSeeder;
use Database\Seeders\TiermakerArtistSeeder;

// php artisan command:Tiermaker
class TiermakerCommand extends Command
{
    protected $signature = 'command:Tiermaker';

    public function handle()
    {

        $this->call(TiermakerArtistSeeder::class);
        //$this->call(TiermakerAlbumSeeder::class);
    }
}
