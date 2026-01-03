<?php

namespace App\Console\Commands\Discogs;

use App\Models\Discogs\DiscogsRelease;
use App\Models\Music\Album;
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
        Logger::echoChannel($this->channel, $this);

        if (!Album::query()->exists()) {
            Logger::log('error', $this->channel, 'No albums found, skipping Discogs Release matcher.', [], $this);

            return;
        }

        $discogsCollectionMatcher = new DiscogsCollectionMatcher($this);

        $processedReleases = [];
        $total = DiscogsRelease::count();

        $this->output->progressStart($total);

        foreach (DiscogsRelease::all() as $release) {
            $processedRelease = $discogsCollectionMatcher->match($release);

            if ($processedRelease) {
                $processedReleases[] = $processedRelease;
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $discogsCollectionMatcher->storeMatches($processedReleases);
        $discogsCollectionMatcher->handleSkipped();
    }
}
