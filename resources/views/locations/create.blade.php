<x-layout>
    <x-slot:heading> Add Location</x-slot:heading>
    <form method="POST" action="/locations">
        @csrf

        <div class="space-y-12">
            <x-input wire:model="location.room_number" label="Room Number" placeholder="BXXX" />
            <x-select
                label="Search Supervisors"
                wire:model.defer="user.id"
                placeholder="Select a supervisor"
                :async-data="route('users.search')"
                option-label="name"
                option-value="id"
            />
            <x-input wire:model="location.description" label="Description" placeholder="Additional information about the location, if needed." />

        </div>
        <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="text-sm/6 font-semibold text-gray-900">Cancel</button>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
    </form>

</x-layout>
