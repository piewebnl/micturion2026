<?php

namespace App\Filament\Resources\AlbumPurchaseResource\Pages;

use App\Filament\Resources\AlbumPurchaseResource;
use App\Models\Music\Album;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlbumPurchase extends EditRecord
{
    protected static string $resource = AlbumPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $album = Album::find($data['album_id']);
        $data['persistent_album_id'] = $album->persistent_id;

        return $data;
    }
}
