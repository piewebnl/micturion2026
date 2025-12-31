<?php

namespace App\Console\Commands\Concert;

use App\Models\Concert\Concert;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;

// php artisan command:ConcertQueryCache
class ConcertQueryCacheCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ConcertQueryCache';

    private string $channel;

    private array $config;

    private Concert $concert;

    public function handle()
    {

        $this->clearCache('get-concerts');

        $this->concert = new Concert;

        $filterValues = [
            'page' => 1,
            'view' => 'grid',
            'sort' => 'date',
            'order' => 'desc',
            'per_page' => 20,
            'keyword' => null,
            'name' => null,
            'year' => null,
            'venue' => null,
            'festival' => null,
        ];
        $this->makeQueries($filterValues);

        $filterValues = [
            'page' => 1,
            'view' => 'grid',
            'sort' => 'date',
            'order' => 'asc',
            'per_page' => 20,
            'keyword' => null,
            'name' => null,
            'year' => null,
            'venue' => null,
            'festival' => null,
        ];
        $this->makeQueries($filterValues);
    }

    private function makeQueries($filterValues)
    {
        $concerts = $this->concert->getConcerts($filterValues, true);
        $lastPage = $concerts->lastPage();
        $perPage = $filterValues['per_page'];
        for ($page = 2; $page <= $lastPage; $page++) {
            $filterValues['per_page'] = $filterValues['per_page'] + $perPage;
            $concerts = $this->concert->getConcerts($filterValues, true);
        }
    }
}
