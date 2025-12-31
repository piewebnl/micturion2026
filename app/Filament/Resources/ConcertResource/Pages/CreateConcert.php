<?php

namespace App\Filament\Resources\ConcertResource\Pages;

use App\Filament\Resources\ConcertResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConcert extends CreateRecord
{
    protected static string $resource = ConcertResource::class;

    protected function afterCreate()
    {
        // Runs after the form fields are saved to the database.
    }
}
