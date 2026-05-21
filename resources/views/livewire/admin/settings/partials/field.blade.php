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
