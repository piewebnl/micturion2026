<?php

namespace App\Helpers;

class JsonHashHelper
{
    public static function hash(array $data): string
    {
        ksort($data);
        array_walk_recursive($data, function (&$value) {
            if (is_array($value)) {
                ksort($value);
            }
        });

        $json = json_encode(
            $data,
            JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_PRESERVE_ZERO_FRACTION
        );

        return hash('sha256', $json);
    }
}
