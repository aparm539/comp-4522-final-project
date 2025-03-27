@extends('two')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Remove Chemical</h2>
    <form action="{{ route('chemicals.destroy') }}" method="POST">
        @csrf
        @method('DELETE')
        <div>
            <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
            <input type="text" name="barcode" id="barcode" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="mt-4">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                Remove Chemical
            </button>
        </div>
    </form>
</div>
@endsection
