<?php

namespace App\Console\Commands\Discogs;

use App\Services\Discogs\Matchers\DiscogsCollectionMatcher;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;

// php artisan command:DiscogsCollectionMatcher
class DiscogsCollectionMatcherCommand extends Command
{
    protected $signature = 'command:DiscogsCollectionMatcher';

    private string $channel = 'discogs_collection_matcher';

    public function handle()
    {

        Logger::deleteChannel($this->channel);

        $discogsCollectionMatcher = new DiscogsCollectionMatcher;
        $discogsCollectionMatcher->match();
    }
}
