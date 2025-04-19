<x-filament-panels::page>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
    @else
        <div>
            {{ $this->form }}
        </div>
    @endif

    @livewire('view-reconciliation', ['reconciliation_id' => $record->id])
</x-filament-panels::page>

