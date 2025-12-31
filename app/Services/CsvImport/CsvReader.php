<?php

namespace App\Services\CsvImport;

class CsvReader
{
    public function read(string $source)
    {

        $rows = [];
        $header = [];

        if (($handle = fopen($source, 'r')) !== false) {

            // Lees header regel
            $header = fgetcsv($handle, 0, ';');

            while (($data = fgetcsv($handle, 0, ';')) !== false) {

                // skip lege regels
                if (!array_filter($data)) {
                    continue;
                }

                // combineer header → waardes
                $rows[] = array_combine($header, $data);
            }

            fclose($handle);
        }

        return $rows;
    }
}
