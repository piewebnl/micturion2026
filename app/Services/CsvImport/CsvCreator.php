<?php

namespace App\Services\CsvImport;

class CsvCreator
{
    public function create(string $file, array $sourceData, array $columns)
    {

        $fp = fopen($file, 'w+');

        $data = [];
        foreach ($sourceData as $index => $fields) {
            foreach ($fields as $key => $field) {
                if (in_array($key, $columns)) {
                    $data[$index][$key] = $field;
                }
            }
        }

        fputcsv($fp, $columns, ';');

        foreach ($data as $key => $field) {
            fputcsv($fp, $field, ';');
        }

        fclose($fp);
    }
}
