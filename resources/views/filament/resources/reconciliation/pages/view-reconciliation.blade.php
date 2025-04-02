<x-filament-panels::page>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
    @else
        <div>
            {{ $this->form }}
        </div>
    @endif

        @livewire('list-reconciliation-items')
</x-filament-panels::page>

