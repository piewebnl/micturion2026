<?php

namespace App\Console\Commands\Discogs;

use App\Models\Discogs\DiscogsRelease;
use App\Models\Discogs\DiscogsReleaseCustomId;
use App\Models\Music\Album;
use App\Services\Discogs\Importers\DiscogsCollectionImporter;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;

// php artisan command:DiscogsCollectionImport
class DiscogsCollectionImportCommand extends Command
{
    protected $signature = 'command:DiscogsCollectionImport';

    private string $channel = 'discogs_collection_importer';

    private int $perPage = 50;

    public function handle()
    {

        Logger::deleteChannel($this->channel);

        // Keep old ids, to delete later
        $releaseIds = DiscogsRelease::pluck('release_id', 'hash')->toArray();

        $idsFromApi = [];

        $discogsCollectionImporter = new DiscogsCollectionImporter($this->perPage);
        $lastPage = $discogsCollectionImporter->getLastPage();

        $count = 0;

        // KEEP TRACK OF DUBLICATES!!!

        if (!$lastPage) {
            Logger::log('error', $this->channel, 'No results from API');
        } else {
            $this->output->progressStart($lastPage);

            for ($page = 1; $page <= $lastPage; $page++) {

                $discogsCollectionImporter = new DiscogsCollectionImporter($this->perPage);
                $discogsCollectionImporter->import($page);

                $importedIds = $discogsCollectionImporter->getIds();

                $idsFromApi = array_merge(
                    $idsFromApi,
                    $importedIds
                );

                $this->output->progressAdvance();
                sleep(1);
                $count = $count + count($importedIds);
            }

            $this->output->progressFinish();
        }

        // Any duplicates?
        $duplicates = array_unique(
            array_diff_assoc($idsFromApi, array_unique($idsFromApi))
        );
        foreach ($duplicates as $duplicate) {
            Logger::log(
                'error',
                $this->channel,
                'Duplictate relase on discogs.com: ' . $duplicate
            );
        }

        // Handle skipped
        $skipped = DiscogsReleaseCustomId::where('release_id', 'skipped')->get();

        foreach ($skipped as $skip) {
            $album = Album::where('persistent_id', $skip['persistent_album_id'])->first();
            $discogsRelease = DiscogsRelease::where('album_id', $album->id)->first();

            if ($discogsRelease) {
                $discogsRelease->release_id = 0;
                $discogsRelease->score = 0;
                $discogsRelease->save();
            }
        }

        // Remove old stuff
        if ($idsFromApi) {
            $diff = array_diff($releaseIds, $idsFromApi);
            DiscogsRelease::whereIn('release_id', $diff)->delete();
            Logger::log(
                'warning',
                $this->channel,
                'Deleted skipped: ' . count($diff)
            );
        }

        Logger::echo($this->channel);
    }
}
