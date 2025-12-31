<?php

namespace App\Services\CsvImport;

use GuzzleHttp\Client;

// Get a json and write to db seedcsv
class JsonToCsvSeedImporter
{
    private $response;

    private string $channel;

    private string $url;

    private string $file;

    private array $columns;

    public function __construct(string $url, string $file, array $columns)
    {
        $this->url = $url;
        $this->file = $file;
        $this->columns = $columns;
    }

    public function import()
    {

        // Import track from live to csv
        $client = new Client(['verify' => false]);
        $res = $client->get($this->url, ['auth' => ['user', 'pass']]);

        if ($res->getStatusCode() == 200) {
            $json = json_decode($res->getBody(), true);
        }

        $csvCreator = new CsvCreator;
        $csvCreator->create($this->file, $json, $this->columns);
    }
}
