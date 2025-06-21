<div x-show="!selectedRecords.length" x-cloak class="flex items-center gap-2">
    @foreach($buttons as $button)
        <x-filament::button
            :color="$button['color'] ?? 'gray'"
            :icon="$button['icon'] ?? null"
            :disabled="true"
            x-tooltip.raw="Select some records first"
        >
            {{ $button['label'] ?? 'Action' }}
        </x-filament::button>
    @endforeach
</div> 