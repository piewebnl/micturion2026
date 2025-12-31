<?php

namespace App\Livewire\Admin\Concerts;

use App\Models\Concert\Concert;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class AdminConcerts extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Concert::query()
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('date')->date('d M Y')->sortable()->searchable(),
                TextColumn::make('concertArtists.concertArtist.name')->sortable()->searchable(),
                TextColumn::make('concertVenue.name')->sortable()->searchable(),

            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->actions([
                Action::make('Edit')
                    ->label('Edit')
                    ->url(fn (Concert $record): string => route('admin.concerts.edit', $record))
                    ->icon('heroicon-o-pencil'),
            ]);
    }

    public function render(): View
    {
        return view(
            'livewire.admin.concerts.admin-concerts'
        );
    }
}
