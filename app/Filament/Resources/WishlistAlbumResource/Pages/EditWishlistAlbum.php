<?php

namespace App\Filament\Resources\WishlistAlbumResource\Pages;

use App\Filament\Resources\WishlistAlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWishlistAlbum extends EditRecord
{
    protected static string $resource = WishlistAlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
