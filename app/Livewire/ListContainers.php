<?php

namespace App\Livewire;

use App\Models\Container;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Tables\Columns\TextColumn;

class ListContainers extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    public $storage_cabinet_id;
    public function mount($storage_cabinet_id)
    {
        $this->storage_cabinet_id = $storage_cabinet_id;
    }
    public function table($table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barcode'),
                TextColumn::make('chemical.cas')
                ->label('CAS #'),
                TextColumn::make('chemical.name')
                    ->limit(25),
                textColumn::make('storageCabinet.name')
                    ->label('Location')
                    ->formatStateUsing(fn ($state, $record) => ($record->storageCabinet->location->room_number.' '.$record->storageCabinet->name)),

            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->query(Container::query()
                ->where('storage_cabinet_id', $this->storage_cabinet_id)
            )
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.list-containers');
    }
}
