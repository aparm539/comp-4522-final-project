<?php

namespace App\Livewire;

use App\Models\Container;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ContainerTable extends PowerGridComponent
{
    public string $tableName = 'container-table-laklxg-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage(5)
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Container::query()->with('location', 'unitofmeasure', 'chemical', 'shelf', 'user');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('barcode')
            ->add('quantity',function ($container) {
                return $container->unitofmeasure->name.' '.$container->quantity;
            })
            ->add('chemical_id',function ($container) {
                return $container->chemical->name;
            })
            ->add('location_id', function ($container) {
                return $container->location->room_number. ' ' .$container->shelf->name;
            })
            ->add('ishazardous')
            ->add('supervisor_id',function ($container) {
                return $container->user->name;
            });

    }

    public function columns(): array
    {
        return [

            Column::make('Barcode', 'barcode')
                ->sortable()
                ->searchable(),

            Column::make('Quantity', 'quantity')
                ->sortable()
                ->searchable(),

            Column::make('Chemical', 'chemical_id')
                ->bodyAttribute('!text-wrap','width: 100px')
                ->sortable()
                ->searchable(),


            Column::make('Location', 'location_id')
                ->sortable()
                ->searchable(),

            Column::make('Hazardous', 'ishazardous')
                ->sortable()
                ->searchable(),

            Column::make('Supervisor', 'supervisor_id')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('created_at'),
        ];
    }

    #[On('edit-click')]
    public function editClick($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions($row): array
    {
        return [
            Button::add('edit-click')
                ->slot('edit')
                ->class('btn btn-sm')
                ->dispatch('edit-click', ['$rowId' => 1]),
        ];
    }


}
