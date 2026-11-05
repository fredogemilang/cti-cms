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
            @php
                if (in_array($key, ['default_locale', 'fallback_locale'], true)) {
                    $rawAvail = $values['available_locales'] ?? 'en,id';
                    $availList = array_values(array_filter(array_map('trim', explode(',', (string) $rawAvail))));
                    if (empty($availList)) {
                        $availList = ['en'];
                    }
                    $isoNames = [
                        'en' => 'English (en)',
                        'id' => 'Bahasa Indonesia (id)',
                        'ms' => 'Bahasa Melayu (ms)',
                        'ja' => 'Japanese (ja)',
                        'zh' => 'Chinese Simplified (zh)',
                        'zh_TW' => 'Chinese Traditional (zh_TW)',
                        'es' => 'Spanish (es)',
                        'fr' => 'French (fr)',
                        'de' => 'German (de)',
                        'ar' => 'Arabic (ar)',
                        'pt' => 'Portuguese (pt)',
                        'ru' => 'Russian (ru)',
                        'ko' => 'Korean (ko)',
                        'it' => 'Italian (it)',
                        'nl' => 'Dutch (nl)',
                        'tr' => 'Turkish (tr)',
                        'vi' => 'Vietnamese (vi)',
                        'th' => 'Thai (th)',
                        'hi' => 'Hindi (hi)',
                        'tl' => 'Filipino (tl)',
                    ];
                    $dynamicOpts = [];
                    foreach ($availList as $code) {
                        $dynamicOpts[$code] = $isoNames[$code] ?? strtoupper($code);
                    }
                    $options = $dynamicOpts;
                }
            @endphp
            <select id="{{ $key }}" wire:model.lazy="{{ $model }}" class="{{ $inputCls }}">
                @foreach($options as $optValue => $optLabel)
                    <option value="{{ $optValue }}">{{ $optLabel }}</option>
                @endforeach
            </select>
            @break

        @case('locales')
            @php
                $initialLocales = array_values(array_filter(array_map('trim', explode(',', (string) ($values[$key] ?? 'en')))));
                if (empty($initialLocales)) {
                    $initialLocales = ['en'];
                }
                $defaultLocaleKey = (string) ($values['default_locale'] ?? 'en');
                $isoLanguagesPreset = [
                    ['code' => 'en', 'name' => 'English', 'native' => 'English'],
                    ['code' => 'id', 'name' => 'Indonesian', 'native' => 'Bahasa Indonesia'],
                    ['code' => 'ms', 'name' => 'Malay', 'native' => 'Bahasa Melayu'],
                    ['code' => 'ja', 'name' => 'Japanese', 'native' => '日本語'],
                    ['code' => 'zh', 'name' => 'Chinese (Simplified)', 'native' => '简体中文'],
                    ['code' => 'zh_TW', 'name' => 'Chinese (Traditional)', 'native' => '繁體中文'],
                    ['code' => 'es', 'name' => 'Spanish', 'native' => 'Español'],
                    ['code' => 'fr', 'name' => 'French', 'native' => 'Français'],
                    ['code' => 'de', 'name' => 'German', 'native' => 'Deutsch'],
                    ['code' => 'ar', 'name' => 'Arabic', 'native' => 'العربية'],
                    ['code' => 'pt', 'name' => 'Portuguese', 'native' => 'Português'],
                    ['code' => 'ru', 'name' => 'Russian', 'native' => 'Русский'],
                    ['code' => 'ko', 'name' => 'Korean', 'native' => '한국어'],
                    ['code' => 'it', 'name' => 'Italian', 'native' => 'Italiano'],
                    ['code' => 'nl', 'name' => 'Dutch', 'native' => 'Nederlands'],
                    ['code' => 'tr', 'name' => 'Turkish', 'native' => 'Türkçe'],
                    ['code' => 'vi', 'name' => 'Vietnamese', 'native' => 'Tiếng Việt'],
                    ['code' => 'th', 'name' => 'Thai', 'native' => 'ไทย'],
                    ['code' => 'hi', 'name' => 'Hindi', 'native' => 'हिन्दी'],
                    ['code' => 'tl', 'name' => 'Filipino', 'native' => 'Tagalog'],
                ];
            @endphp
            <div
                x-data="{
                    locales: @js($initialLocales),
                    defaultLocale: @js($defaultLocaleKey),
                    presetLanguages: @js($isoLanguagesPreset),
                    openDropdown: false,
                    search: '',
                    customCode: '',
                    
                    init() {
                        this.$watch('$wire.values.{{ $key }}', (val) => {
                            if (typeof val === 'string') {
                                this.locales = val.split(',').map(s => s.trim()).filter(Boolean);
                            }
                        });
                        this.$watch('$wire.values.default_locale', (val) => {
                            if (val) {
                                this.defaultLocale = val;
                            }
                        });
                    },

                    get filteredLanguages() {
                        let q = this.search.toLowerCase().trim();
                        return this.presetLanguages.filter(l => {
                            if (this.locales.includes(l.code)) return false;
                            if (!q) return true;
                            return l.name.toLowerCase().includes(q) || l.native.toLowerCase().includes(q) || l.code.toLowerCase().includes(q);
                        });
                    },

                    getLanguageLabel(code) {
                        let found = this.presetLanguages.find(l => l.code === code);
                        return found ? (found.native + ' (' + code + ')') : code.toUpperCase();
                    },

                    addLocale(code) {
                        let clean = code.toLowerCase().trim();
                        if (!clean || this.locales.includes(clean)) return;
                        this.locales.push(clean);
                        this.sync();
                        this.search = '';
                        this.openDropdown = false;
                    },

                    removeLocale(code) {
                        if (code === this.defaultLocale) {
                            return;
                        }
                        this.locales = this.locales.filter(l => l !== code);
                        this.sync();
                    },

                    addCustom() {
                        let c = (this.customCode || this.search).toLowerCase().replace(/[^a-z0-9_]/g, '').trim();
                        if (c.length >= 2) {
                            this.addLocale(c);
                            this.customCode = '';
                            this.search = '';
                        }
                    },

                    sync() {
                        let val = this.locales.join(',');
                        $wire.set('values.{{ $key }}', val);
                    }
                }"
                class="space-y-3"
            >
                <input type="hidden" id="{{ $key }}" wire:model.lazy="{{ $model }}" :value="locales.join(',')" />

                <div class="flex flex-wrap items-center gap-2 p-3 min-h-[52px] rounded-2xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0F1113]/50">
                    <template x-for="code in locales" :key="code">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold shadow-xs transition"
                            :class="code === defaultLocale 
                                ? 'bg-blue-50 dark:bg-blue-950/50 text-[#2563EB] dark:text-blue-300 border border-blue-200 dark:border-blue-800/60' 
                                : 'bg-white dark:bg-[#1A1A1A] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30]'"
                        >
                            <span class="material-symbols-outlined text-[16px] text-[#2563EB]">language</span>
                            <span x-text="getLanguageLabel(code)"></span>

                            <template x-if="code === defaultLocale">
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-[#2563EB] text-white text-[10px] font-bold tracking-wider uppercase" title="Primary default locale (cannot be removed)">
                                    Default ★
                                </span>
                            </template>

                            <template x-if="code !== defaultLocale">
                                <button
                                    type="button"
                                    x-on:click="removeLocale(code)"
                                    class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 ml-1 rounded-full p-0.5 transition cursor-pointer"
                                    title="Remove language"
                                >
                                    <span class="material-symbols-outlined text-[14px] leading-none">close</span>
                                </button>
                            </template>
                        </div>
                    </template>

                    <div class="relative" x-on:click.outside="openDropdown = false">
                        <button
                            type="button"
                            x-on:click="openDropdown = !openDropdown"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-[#1A1A1A] border border-dashed border-gray-300 dark:border-[#272B30] text-[#2563EB] hover:bg-blue-50/50 dark:hover:bg-blue-950/20 transition cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>Add Language</span>
                        </button>

                        <div
                            x-show="openDropdown"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-72 rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] p-2.5 shadow-xl z-50 space-y-2"
                            style="display: none;"
                        >
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[16px] text-gray-400">search</span>
                                <input
                                    type="text"
                                    x-model="search"
                                    placeholder="Search language or code..."
                                    class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0F1113] text-[#111827] dark:text-[#FCFCFC] focus:outline-none focus:ring-1 focus:ring-[#2563EB]"
                                    x-on:keydown.enter.prevent="if (filteredLanguages.length > 0) addLocale(filteredLanguages[0].code); else addCustom();"
                                />
                            </div>

                            <div class="max-h-56 overflow-y-auto space-y-1 py-1 pr-1 text-xs">
                                <template x-for="lang in filteredLanguages" :key="lang.code">
                                    <button
                                        type="button"
                                        x-on:click="addLocale(lang.code)"
                                        class="w-full text-left flex items-center justify-between px-2.5 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-[#272B30] text-[#111827] dark:text-[#FCFCFC] transition cursor-pointer"
                                    >
                                        <span class="flex items-center gap-2">
                                            <span class="font-medium" x-text="lang.native"></span>
                                            <span class="text-[11px] text-[#6F767E]" x-text="'(' + lang.name + ')'"></span>
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-gray-100 dark:bg-[#272B30] text-[#6F767E]" x-text="lang.code"></span>
                                    </button>
                                </template>

                                <template x-if="filteredLanguages.length === 0 && search.trim() !== ''">
                                    <div class="p-2 text-center text-[#6F767E] text-[11px]">
                                        <p>No preset matching "<span class="font-bold text-[#111827] dark:text-[#FCFCFC]" x-text="search"></span>"</p>
                                        <button
                                            type="button"
                                            x-on:click="addCustom()"
                                            class="mt-2 inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-[#2563EB] text-white text-xs font-bold hover:bg-blue-700 transition"
                                        >
                                            <span class="material-symbols-outlined text-[14px]">add</span>
                                            Add custom code "<span x-text="search.toLowerCase().replace(/[^a-z0-9_]/g, '')"></span>"
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
