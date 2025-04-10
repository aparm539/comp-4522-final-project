<?php

namespace App\Filament\Exports;

use App\Models\Container;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;

class ContainerExporter extends Exporter
{
    protected static ?string $model = Container::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('barcode')
                ->label('Barcode'),
            ExportColumn::make('chemical.cas')
                ->label('CAS #'),
            ExportColumn::make('chemical.name')
                ->label('Chemical Name'),
            ExportColumn::make('quantity')
                ->label('Quantity'),
            ExportColumn::make('unitofmeasure.abbreviation')
                ->label('Unit'),
            ExportColumn::make('storageCabinet.location.room_number')
                ->label('Room'),
            ExportColumn::make('storageCabinet.name')
                ->label('Cabinet'),
            ExportColumn::make('chemical.ishazardous')
                ->label('Hazardous'),
            ExportColumn::make('storageCabinet.location.user.name')
                ->label('Supervisor'),
        ];
    }

    public static function getColumnLabels(): array
    {
        return [
            'barcode' => 'Barcode',
            'chemical.cas' => 'CAS #',
            'chemical.name' => 'Chemical Name',
            'quantity' => 'Quantity',
            'unitofmeasure.abbreviation' => 'Unit',
            'storageCabinet.location.room_number' => 'Room',
            'storageCabinet.name' => 'Cabinet',
            'chemical.ishazardous' => 'Hazardous',
            'storageCabinet.location.user.name' => 'Supervisor',
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your container export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
} 