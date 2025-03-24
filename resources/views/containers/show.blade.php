<x-layout>
<x-slot:heading> container view
</x-slot:heading>

    <p>{{$container['barcode']}}</p>
    <p>{{$container['quantity']}}</p>
    <p>{{$container['unit_of_measure']}}</p>
    <p>{{$container['chemical_id']}}</p>
    <p>{{$container['location_id']}}</p>
    <p>{{$container['shelf_id']}}</p>
    <p>{{$container['ishazardous']}}</p>
    <p>{{$container['date_added']}}</p>
    <p>{{$container['supervisor_id']}}</p>
</x-layout>
