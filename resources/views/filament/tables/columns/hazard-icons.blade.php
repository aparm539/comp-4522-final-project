@php
    $record = $getRecord();

    $hazardClasses = collect();

    if (isset($record->whmisHazardClasses)) {
        $hazardClasses = $record->whmisHazardClasses;
    }

    if ($hazardClasses->isEmpty() && isset($record->chemical) && isset($record->chemical->whmisHazardClasses)) {
        $hazardClasses = $record->chemical->whmisHazardClasses;
    }
@endphp

<div class="flex items-center justify-center gap-2 flex-wrap">
    @foreach ($hazardClasses as $hazardClass)
        <span class="inline-flex items-center gap-1">
            @if(!($hazardClass->icon ==="blank_square"))
                <x-filament::icon :icon="$hazardClass->icon" class="w-4 h-4 text-primary-600" />
            @endif
            <span class="text-xs">{{ $hazardClass->class_name }}</span>
        </span>
    @endforeach
</div>
