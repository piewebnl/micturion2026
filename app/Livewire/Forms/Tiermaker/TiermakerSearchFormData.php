<?php

namespace App\Livewire\Forms\Tiermaker;

use App\Services\Forms\FormDataGenerator;
use App\Traits\QueryCache\QueryCache;

class TiermakerSearchFormData
{
    use QueryCache;

    public function generate(): array
    {
        $formDataGenerator = new FormDataGenerator;
        $formData = [];

        return $formData;
    }
}
