<?php

namespace App\Console\Commands\Discogs;

use App\Models\Discogs\DiscogsRelease;
use App\Services\Discogs\Importers\DiscogsReleaseInfoImporter;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

// php artisan command:DiscogsReleaseInfoImport
class DiscogsReleaseInfoImportCommand extends Command
{
    protected $signature = 'command:DiscogsReleaseInfoImport';

    private string $channel;

    public function handle()
    {

        $this->channel = 'discogs_release_info_importer';

        Logger::deleteChannel($this->channel);

        Logger::echoChannel($this->channel);

        $discogsReleases = DiscogsRelease::where('status', 'imported')->orWhere('updated_at', '<', Carbon::now()->subMonths(1))->get();
        $lastPage = $discogsReleases->count();

        $this->output->progressStart($lastPage);

        foreach ($discogsReleases as $discogsRelease) {
            $discogsReleaseInfoImporter = new DiscogsReleaseInfoImporter($discogsRelease);
            $discogsReleaseInfoImporter->import();
            $this->output->progressAdvance();
            sleep(3);
        }

        $this->output->progressFinish();

        // Logger::echo($this->channel);
    }
}
