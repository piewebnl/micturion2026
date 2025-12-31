<?php

namespace App\Filament\Resources\MusicStoreResource\Pages;

use App\Filament\Resources\MusicStoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMusicStores extends ListRecords
{
    protected static string $resource = MusicStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
