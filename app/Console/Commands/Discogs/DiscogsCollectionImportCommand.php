<?php

namespace App\Console\Commands\Discogs;

use App\Models\Discogs\DiscogsRelease;
use App\Services\Discogs\Importers\DiscogsCollectionImporter;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;

// php artisan command:DiscogsCollectionImport
class DiscogsCollectionImportCommand extends Command
{
    protected $signature = 'command:DiscogsCollectionImport';

    protected $description = 'Imports all Discogs releases (online) via its api';

    private string $channel = 'discogs_collection_importer';

    private int $perPage = 50;

    public function handle()
    {
        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $releaseIds = DiscogsRelease::pluck('release_id', 'hash')->toArray();
        $idsFromApi = [];

        $discogsCollectionImporter = new DiscogsCollectionImporter($this->perPage, $this);
        $discogsCollectionImporter->setReleaseIds($releaseIds);

        $lastPage = $discogsCollectionImporter->getLastPage();

        if (!$lastPage) {
            Logger::log('error', $this->channel, 'No results from API');

            return;
        }

        $this->output->progressStart($lastPage);

        for ($page = 1; $page <= $lastPage; $page++) {
            $importedIds = $discogsCollectionImporter->importPage($page);
            $idsFromApi = array_merge($idsFromApi, $importedIds);

            $this->output->progressAdvance();
            // sleep(1);
        }

        $this->output->progressFinish();

        $discogsCollectionImporter->finalizeImport($idsFromApi);
    }
}
