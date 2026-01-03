<?php

namespace App\Console\Commands\DatabaseDumper;

use App\Services\CsvImport\JsonToCsvSeedImporter;
use App\Services\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

// php artisan command:DatabaseDumper
class DatabaseDumperCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:DatabaseDumper';

    protected $description = 'Grabs data from models (online) and dumps it to csv files (to later DB seed)';

    private string $channel = 'database_dumber';

    private array $filesToImport;

    public function handle()
    {
        if (App::environment() != 'local') {
            return;
        }

        $this->filesToImport = config('csv-import');

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $this->output->progressStart(count($this->filesToImport));

        foreach ($this->filesToImport as $filesToImport) {

            $jsonToCsvSeedImporter = new JsonToCsvSeedImporter(
                $filesToImport['url'],
                $filesToImport['file'],
                $filesToImport['columns'],
            );
            $jsonToCsvSeedImporter->import();
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
