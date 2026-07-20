@props([
    'name',
    'label' => null,
    'type' => 'text',
    'required' => false
])

<div>
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-bold text-gray-700 dark:text-[#FCFCFC] mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    @endif
    
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#0B0B0B] text-gray-900 dark:text-[#FCFCFC] focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/30 transition ' . ($errors->has($name) ? 'border-red-500 dark:border-red-500' : '')]) }}
    />
    
    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
