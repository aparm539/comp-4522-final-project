<div>
    <x-filament::card>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium">Current Role: {{ $this->getCurrentRole() }}</h3>
                <p class="text-sm text-gray-500">This is for demo purposes only</p>
            </div>
            
            <x-filament::button
                wire:click="$dispatch('open-modal', { id: 'change-role' })"
                color="primary"
            >
                Change Role
            </x-filament::button>
        </div>

        <x-filament::modal
            id="change-role"
            :close-button="true"
            :close-by-clicking-away="true"
            width="md"
        >
            <x-slot name="heading">
                Change Your Role
            </x-slot>

            <form wire:submit="changeRole">
                {{ $this->form }}
            </form>

            <x-slot name="footerActions">
                <x-filament::button
                    type="submit"
                    form="changeRole"
                    color="primary"
                >
                    Save Changes
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </x-filament::card>
</div> 