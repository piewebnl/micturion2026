<?php

namespace App\Filament\Resources\ConcertFestivalResource\Pages;

use App\Filament\Resources\ConcertFestivalResource;
use App\Models\Concert\ConcertFestivalImage;
use App\Traits\QueryCache\QueryCache;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConcertFestival extends EditRecord
{
    use QueryCache;

    protected static string $resource = ConcertFestivalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {

        $concertFestival = $this->record;

        $concertFesitvalImage = new ConcertFestivalImage;
        $concertFesitvalImage->create(
            $concertFestival,
            storage_path('app/public/' . ltrim($concertFestival->image_url, '/'))
        );

        $slug = $concertFesitvalImage->getConcertFestivalImageSlug($concertFestival);

        $concertFestival->update([
            'image_url' => '/images/festivals/' . $slug . '.jpg',
        ]);

        $this->clearCache('get-concerts');
    }
}
