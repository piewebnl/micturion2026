<?php

namespace App\Filament\Resources\AlbumPurchaseResource\Pages;

use App\Filament\Resources\AlbumPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlbumPurchase extends ListRecords
{
    protected static string $resource = AlbumPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
