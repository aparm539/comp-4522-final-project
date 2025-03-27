@extends('two')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Add New Chemical</h2>
    <form action="{{ route('chemicals.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
                <input type="text" name="barcode" id="barcode" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="cas" class="block text-sm font-medium text-gray-700">CAS Number</label>
                <input type="text" name="cas" id="cas" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Add Chemical
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
