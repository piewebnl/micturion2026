<?php

namespace App\Console\Commands\DatabaseDumper;

use App\Services\CsvImport\JsonToCsvSeedImporter;
use App\Traits\Logger\Logger;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

// php artisan command:DatabaseDumper
class DatabaseDumperCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:DatabaseDumper';

    private string $channel;

    private array $filesToImport;

    public function handle()
    {
        if (App::environment() != 'local') {
            return;
        }

        $this->channel = 'database_dumber';
        $this->filesToImport = config('csv-import');

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel);
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
        Cache::flush();

        $this->output->progressFinish();
        Logger::echo($this->channel);
    }
}
