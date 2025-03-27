@extends('two')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Edit Chemical</h2>
    <form action="{{ route('chemicals.update', $chemical->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
            <input type="text" name="barcode" id="barcode" value="{{ $chemical->barcode }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="mt-4">
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                Update Chemical
            </button>
        </div>
    </form>
</div>
@endsection
