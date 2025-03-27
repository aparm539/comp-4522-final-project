<x-layout>
    <x-slot:heading> Locations </x-slot:heading>
    @foreach ($locations as $location)

        <a href="locations/{{$location['id']}}" class="block border-2 border-gray-300 rounded-md p-4 m-4">
            <p> {{ $location->room_number }} </p>
            <p> {{ $location->description }} </p>
        </a>
        @endforeach
    <div>
        {{ $locations->links() }}
    </div>
</x-layout>
