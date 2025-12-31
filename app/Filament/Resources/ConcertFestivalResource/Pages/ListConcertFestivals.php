<?php

namespace App\Filament\Resources\ConcertFestivalResource\Pages;

use App\Filament\Resources\ConcertFestivalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConcertFestivals extends ListRecords
{
    protected static string $resource = ConcertFestivalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
