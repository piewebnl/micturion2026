<?php

namespace App\Console\Commands\Wishlist;

use App\Services\CsvImport\JsonToCsvSeedImporter;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:WishlistAlbumImportCsv
class WishlistAlbumImportCsvCommand extends Command
{
    protected $signature = 'command:WishlistAlbumImportCsv';

    private string $channel;

    private array $filesToImport;

    public function handle()
    {
        $this->channel = 'wishlist_album_import_csv';
        $this->filesToImport = config('csv-import.wishlist_album_import');

        if (App::environment() != 'local') {
            return;
        }

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel);

        $this->output->progressStart(1);

        $jsonToCsvSeedImporter = new JsonToCsvSeedImporter(
            $this->filesToImport['url'],
            $this->filesToImport['file'],
            $this->filesToImport['columns'],
        );
        $jsonToCsvSeedImporter->import();
        $this->output->progressAdvance();

        $this->output->progressFinish();
        Logger::echo($this->channel);
    }
}
