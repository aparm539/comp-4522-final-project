<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }} }"
        class="grid grid-cols-2 gap-2"
    >
        @foreach ($classes() as $hazardClass)
            <label class="flex items-center gap-2">
                <input
                    type="checkbox"
                    x-model="state"
                    value="{{ $hazardClass->id }}"
                    class="fi-checkbox h-4 w-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500"
                />
                <x-filament::icon :icon="$hazardClass->heroicon" class="w-5 h-5 text-primary-600" />
                <span class="text-sm text-gray-700">{{ $hazardClass->class_name }}</span>
            </label>
        @endforeach
    </div>
</x-dynamic-component> 