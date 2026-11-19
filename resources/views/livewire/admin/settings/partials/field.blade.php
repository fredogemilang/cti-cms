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
    @if($type !== 'boolean' && $type !== 'info')
        <label for="{{ $key }}" class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">
            {{ $label }}
        </label>
    @endif

    @switch($type)
        @case('info')
            @php
                $isLiteSpeed = \App\Services\CacheManager::isLiteSpeed();
                $icon = $field['icon'] ?? ($isLiteSpeed ? 'verified' : 'info');
                $isSuccess = ($field['variant'] ?? '') === 'success' || ($isLiteSpeed && !isset($field['variant']));
                $boxCls = $isSuccess
                    ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300'
                    : 'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/20 text-blue-800 dark:text-blue-300';
                $iconCls = $isSuccess ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#2563EB] dark:text-blue-400';
            @endphp
            <div class="rounded-2xl border p-4 text-xs flex items-start gap-3.5 {{ $boxCls }}">
                <span class="material-symbols-outlined text-[22px] shrink-0 mt-0.5 {{ $iconCls }}">{{ $icon }}</span>
                <div class="space-y-1 min-w-0 flex-1">
                    @if($label && $label !== $key)
                        <div class="font-bold text-sm text-[#111827] dark:text-[#FCFCFC]">{{ $label }}</div>
                    @endif
                    <div class="text-xs leading-relaxed text-[#4B5563] dark:text-[#9CA3AF]">
                        {!! $field['content'] ?? ($field['help'] ?? '') !!}
                    </div>
                </div>
            </div>
            @break

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
            <div>
                <livewire:admin.media-picker 
                    :field="$key"
                    :value="$values[$key] ?? null"
                    :label="$label"
                    :compact="true"
                    :key="'settings-media-picker-' . $key"
                />
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

    @if($help && $type !== 'boolean' && $type !== 'info')
        <p class="text-xs text-[#6F767E] mt-1.5">{{ $help }}</p>
    @endif

    @error("values.{$key}")
        <p class="text-xs text-[#FF6A55] mt-1.5 flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">error</span>
            {{ $message }}
        </p>
    @enderror
</div>
