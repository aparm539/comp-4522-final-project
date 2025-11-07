<?php

declare(strict_types=1);

namespace App\Livewire;

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
            if ($record->icon === 'blank_square') {
                return '<span class="inline-flex items-start gap-2">'
                   .'<span>'.e($record->class_name).'</span>'
                    .'</span>';
            }

            return '<span class="inline-flex items-center gap-2 w-full">'
                .view('filament::components.icon', [
                    'icon' => $record->icon,
                    'class' => 'w-10 h-10 text-primary-600 shrink-0',
                ])->render()
                .'<span class="text-xs font-medium leading-tight break-words flex-1 min-w-0">'.e($record->class_name).'</span>'
                .'</span>';
        });

        $this->allowHtml();

        $this->columns(3);
    }
}
