<?php

namespace App\Livewire;

use App\Models\Container;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ContainerHistory extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $containerId;

    public function mount(int $containerId)
    {
        $this->containerId = $containerId;
    }

    public function history(): array{
        return DB::select("
  SELECT *, ROW_START, ROW_END FROM containers FOR SYSTEM_TIME ALL WHERE id = $this->containerId
");
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Container::bySystemTime()->where('id', $this->containerId))
            ->columns([
                TextColumn::make('ROW_START')
                    ->dateTime()
                    ->label('Start Date'),
                TextColumn::make('ROW_END')
                    ->label('End Date')
                    ->dateTime(),
                TextColumn::make('quantity')
                    ->label('Quantity'),
            ]);
    }

    public function render()
    {
        return view('livewire.container-history');
    }
}
