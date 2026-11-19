@props([
    'name',
    'label',
    'checked' => false,
    'description' => null
])

<div>
    <label class="flex items-center p-4 bg-white/50 dark:bg-[#1A1A1A]/50 rounded-2xl hover:bg-white dark:hover:bg-[#1A1A1A]/80 transition border border-transparent dark:border-[#272B30] cursor-pointer">
        <input 
            type="checkbox" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="1" 
            {{ $checked ? 'checked' : '' }}
            {{ $attributes->merge(['class' => 'w-5 h-5 text-blue-600 border-gray-300 dark:border-[#272B30] rounded focus:ring-blue-500 dark:bg-[#0B0B0B]']) }}
        />
        <div class="ml-3">
            <span class="text-sm font-bold text-gray-900 dark:text-[#FCFCFC]">{{ $label }}</span>
            @if($description)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $description }}</p>
            @endif
        </div>
    </label>
</div>
