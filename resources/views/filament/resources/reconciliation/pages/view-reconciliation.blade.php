<x-filament-panels::page>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
    @else
        <div>
            {{ $this->form }}
        </div>
    @endif

    @livewire('list-reconciliation-items', ['reconciliation_id' => $record->id])
</x-filament-panels::page>

