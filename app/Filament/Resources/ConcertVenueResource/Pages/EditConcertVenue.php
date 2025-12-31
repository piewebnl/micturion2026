<?php

namespace App\Filament\Resources\ConcertVenueResource\Pages;

use App\Filament\Resources\ConcertVenueResource;
use App\Models\Concert\ConcertVenue;
use App\Models\Concert\ConcertVenueImage;
use App\Traits\QueryCache\QueryCache;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConcertVenue extends EditRecord
{
    use QueryCache;

    protected static string $resource = ConcertVenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave()
    {
        if (isset($this->data['image_url'])) {

            foreach ($this->data['image_url'] as $image) {

                $item = new ConcertVenue;
                $item->fill($this->data);

                $imageCreator = new ConcertVenueImage;
                // $response = $imageCreator->create($item, storage_path() . '/app/public/' . $image);

                $item->image_url = '/storage/images/festivals/' . // $response->slug;
                $item->update();
            }
        }

        $this->clearCache('get-concerts');
    }
}
