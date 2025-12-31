<?php

namespace App\Filament\Resources\ConcertResource\Pages;

use App\Filament\Resources\ConcertResource;
use App\Models\Concert\ConcertImage;
use App\Traits\QueryCache\QueryCache;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConcert extends EditRecord
{
    protected static string $resource = ConcertResource::class;

    use QueryCache;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        foreach ($this->record->concertItems as $concertItem) {

            $concertImage = new ConcertImage;
            $concertImage->create(
                $concertItem,
                storage_path('app/public/' . ltrim($concertItem->image_url, '/'))
            );

            $slug = $concertImage->getConcertImageSlug($concertItem);

            $concertItem->update([
                'image_url' => '/images/concerts/' . $slug . '.jpg',
            ]);
        }

        $this->clearCache('get-concerts');
    }
}
