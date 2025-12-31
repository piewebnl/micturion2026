<?php

namespace App\Helpers;

class SearchFormHelper
{
    public static function check_bool(mixed $value, bool $default = true): bool
    {
        return match ($value) {
            'false', false => false,
            'true', true => true,
            default => $default,
        };
    }

    public static function checkByLabel($formData, array $labels = [])
    {
        return collect($formData)->whereIn('label', $labels)->pluck('value')->toArray();
    }

    public static function checkAll($formData, array $rejectLabels = [])
    {

        return collect($formData)
            ->reject(function ($item) use ($rejectLabels) {
                return in_array($item['label'], $rejectLabels);
            })
            ->pluck('value')
            ->toArray();
    }
}
