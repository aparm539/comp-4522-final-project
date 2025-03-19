<x-layout>
    <x-slot:heading> Lab B220 </x-slot:heading>
    @foreach($containers as $container)
        <div class="block border-2 border-gray-300 rounded-md p-4 m-4">
            <h1> Name: {{$container['chemical_name']}}</h1>
            <h1> Amount: {{$container['quantity']}}</h1>
            <h1> Room: {{$container->location->room_number}}</h1>
            <h1> Shelf: {{$container->location->shelf}}</h1>
        </div>
    @endforeach
    <div>
        {{$containers->links()}}
    </div>
</x-layout>
