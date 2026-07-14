@php
    $key      = $field['key'];
    $type     = $field['type'] ?? 'text';
    $label    = $field['label'] ?? $key;
    $help     = $field['help'] ?? null;
    $options  = $field['options'] ?? [];
    $model    = "values.{$key}";
    $inputCls = 'w-full rounded-xl border border-gray-300 dark:border-[#272B30] bg-white dark:bg-[#0F1113] px-4 py-2.5 text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:outline-none transition';
@endphp

<div>
    @if($type !== 'boolean')
        <label for="{{ $key }}" class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">
            {{ $label }}
        </label>
    @endif

    @switch($type)
        @case('textarea')
            <textarea
                id="{{ $key }}"
                wire:model.lazy="{{ $model }}"
                rows="4"
                class="{{ $inputCls }}"
            ></textarea>
            @break

        @case('select')
            <select id="{{ $key }}" wire:model.lazy="{{ $model }}" class="{{ $inputCls }}">
                @foreach($options as $optValue => $optLabel)
                    <option value="{{ $optValue }}">{{ $optLabel }}</option>
                @endforeach
            </select>
            @break

        @case('boolean')
            <label for="{{ $key }}" class="flex items-center justify-between gap-4 cursor-pointer">
                <span>
                    <span class="block text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $label }}</span>
                    @if($help)
                        <span class="block text-xs text-[#6F767E] mt-1">{{ $help }}</span>
                    @endif
                </span>
                <input
                    id="{{ $key }}"
                    type="checkbox"
                    wire:model.lazy="{{ $model }}"
                    class="h-5 w-10 rounded-full appearance-none bg-gray-300 dark:bg-[#272B30] checked:bg-[#2563EB] relative cursor-pointer transition
                        before:content-[''] before:absolute before:top-0.5 before:left-0.5 before:h-4 before:w-4 before:rounded-full before:bg-white before:transition
                        checked:before:translate-x-5"
                />
            </label>
            @break

        @case('number')
            <input
                id="{{ $key }}"
                type="number"
                wire:model.lazy="{{ $model }}"
                class="{{ $inputCls }}"
            />
            @break

        @case('email')
            <input
                id="{{ $key }}"
                type="email"
                wire:model.lazy="{{ $model }}"
                class="{{ $inputCls }}"
            />
            @break

        @case('password')
            <input
                id="{{ $key }}"
                type="password"
                wire:model.lazy="{{ $model }}"
                autocomplete="new-password"
                class="{{ $inputCls }}"
            />
            @break

        @case('media')
            <div class="flex items-start gap-4">
                @if(!empty($values[$key] ?? null))
                    <div class="relative group shrink-0">
                        <img src="{{ asset('storage/' . $values[$key]) }}" alt="{{ $label }}" class="h-20 w-20 rounded-xl object-cover border border-gray-200 dark:border-[#272B30]">
                        <button type="button" wire:click="$set('{{ $model }}', '')"
                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif
                <button type="button"
                    wire:click="$dispatch('open-media-picker', { field: '{{ $key }}' })"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#272B30] text-sm font-medium text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:border-gray-300 dark:hover:border-[#333] transition bg-white dark:bg-[#0F1113]">
                    <span class="material-symbols-outlined text-lg">{{ empty($values[$key] ?? null) ? 'add_photo_alternate' : 'swap_horiz' }}</span>
                    {{ empty($values[$key] ?? null) ? 'Choose image...' : 'Change' }}
                </button>
            </div>
            @break

        @case('code')
            <textarea
                id="{{ $key }}"
                wire:model.lazy="{{ $model }}"
                rows="6"
                spellcheck="false"
                class="{{ $inputCls }} font-mono text-xs"
            ></textarea>
            @break

        @default
            <input
                id="{{ $key }}"
                type="text"
                wire:model.lazy="{{ $model }}"
                class="{{ $inputCls }}"
            />
    @endswitch

    @if($help && $type !== 'boolean')
        <p class="text-xs text-[#6F767E] mt-1.5">{{ $help }}</p>
    @endif

    @error("values.{$key}")
        <p class="text-xs text-[#FF6A55] mt-1.5 flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">error</span>
            {{ $message }}
        </p>
    @enderror
</div>
