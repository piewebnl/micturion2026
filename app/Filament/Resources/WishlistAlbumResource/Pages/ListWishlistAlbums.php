<?php

namespace App\Filament\Resources\WishlistAlbumResource\Pages;

use App\Filament\Resources\WishlistAlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWishlistAlbums extends ListRecords
{
    protected static string $resource = WishlistAlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
