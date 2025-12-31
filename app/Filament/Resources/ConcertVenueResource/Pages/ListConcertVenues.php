<?php

namespace App\Filament\Resources\ConcertVenueResource\Pages;

use App\Filament\Resources\ConcertVenueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConcertVenues extends ListRecords
{
    protected static string $resource = ConcertVenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
