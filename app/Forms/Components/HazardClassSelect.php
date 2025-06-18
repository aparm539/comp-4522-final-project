<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Models\WhmisHazardClass;
use Filament\Forms\Components\CheckboxList;

/**
 * Custom multi-select field that displays WHMIS hazard class icons.
 */
final class HazardClassSelect extends CheckboxList
{
    protected function setUp(): void
    {
        parent::setUp();

        // Render label for each record with icon + name.
        $this->getOptionLabelFromRecordUsing(function (WhmisHazardClass $record): string {
            return view('filament::components.icon', [
                'icon'  => $record->icon,
                'class' => 'w-4 h-4 inline-block text-primary-600 mr-1',
            ])->render().' '.$record->class_name;
        });

        $this->allowHtml();

        // Display in two columns.
        $this->columns(2);

        $this->label('WHMIS Hazard Classes');
    }
} 