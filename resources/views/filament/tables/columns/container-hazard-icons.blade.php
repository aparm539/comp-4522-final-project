<div class="flex items-center justify-center gap-2 flex-wrap">
    @foreach ($getRecord()->chemical->whmisHazardClasses as $hazardClass)
        <span class="inline-flex items-center gap-1">
            <x-filament::icon :icon="$hazardClass->icon" class="w-4 h-4 text-primary-600" />
            <span class="text-xs">{{ $hazardClass->class_name }}</span>
        </span>
    @endforeach
</div> 