<?php

namespace App\Console\Commands\Discogs;

use App\Models\Discogs\DiscogsRelease;
use App\Services\Discogs\Importers\DiscogsReleaseInfoImporter;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

// php artisan command:DiscogsReleaseInfoImport
class DiscogsReleaseInfoImportCommand extends Command
{
    protected $signature = 'command:DiscogsReleaseInfoImport';

    protected $description = 'Imports all extra info (artwork etc) of Discogs releases (online) via its api';

    private string $channel = 'discogs_release_info_importer';

    public function handle()
    {

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $discogsReleases = DiscogsRelease::where('status_info', null)->orWhere('status', 'custom')->orWhere('updated_at', '<', Carbon::now()->subMonths(1))->get();

        if ($discogsReleases->isEmpty()) {
            Logger::log('error', $this->channel, 'No discogs releases', [], $this);

            return;
        }

        $lastPage = $discogsReleases->count();

        $this->output->progressStart($lastPage);

        foreach ($discogsReleases as $discogsRelease) {
            $discogsReleaseInfoImporter = new DiscogsReleaseInfoImporter($discogsRelease);
            $discogsReleaseInfoImporter->import();
            $this->output->progressAdvance();
            // sleep(3);
        }

        $this->output->progressFinish();
    }
}
