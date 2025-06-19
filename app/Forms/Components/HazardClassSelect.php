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
            return '<span class="inline-flex items-start gap-2">'
                . view('filament::components.icon', [
                    'icon'  => $record->icon,
                    'class' => 'w-8 h-8 text-primary-600 shrink-0',
                ])->render()
                . '<span>' . e($record->class_name) . '</span>'
                . '</span>';
        });

        $this->allowHtml();

        // Display in two columns.
        $this->columns(2);

        $this->label('WHMIS Hazard Classes');
    }
} 