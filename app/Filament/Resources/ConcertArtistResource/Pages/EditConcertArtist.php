<?php

namespace App\Filament\Resources\ConcertArtistResource\Pages;

use App\Filament\Resources\ConcertArtistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConcertArtist extends EditRecord
{
    protected static string $resource = ConcertArtistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
