<x-layout>
    <x-slot:heading> All Inventory </x-slot:heading>
    @foreach($containers as $container)
        <div class="block border-2 border-gray-300 rounded-md p-4 m-4">
            <p> Chemical name: {{$container->chemical->full_name}}</p>
            <p> CAS: {{$container->chemical->cas}}</p>
            <p> barcode: {{$container['barcode']}}</p>
            <p> Amount: {{$container['quantity']}} {{$container->unitofmeasure->abbreviation}}</p>
            <p> Location: {{$container->location->room_number}} {{$container->shelf->name}}</p>
        </div>
    @endforeach
    <div>
        {{$containers->links()}}
    </div>
</x-layout>
