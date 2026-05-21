@section('title', $currentGroup['label'] . ' Settings')
@section('page-title', $currentGroup['label'] . ' Settings')

<div>
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Group navigation --}}
        <aside class="lg:w-64 shrink-0">
            <nav class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-2 sticky top-6">
                @foreach($allGroups as $g)
                    @if(auth()->user()->hasPermission($g['permission'] ?? 'settings.view') || auth()->user()->isSuperAdmin())
                        <a
                            wire:navigate
                            href="{{ route('admin.settings.show', $g['slug']) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                                {{ $g['slug'] === $group
                                    ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-white'
                                    : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50 hover:text-[#111827] dark:hover:text-white' }}"
                        >
                            <span class="material-symbols-outlined text-[20px]">{{ $g['icon'] }}</span>
                            {{ $g['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </aside>

        {{-- Form area --}}
        <main class="flex-1 min-w-0">
            @if($currentGroup['description'] ?? null)
                <p class="text-sm text-[#6F767E] mb-6">{{ $currentGroup['description'] }}</p>
            @endif

            @if(!empty($currentGroup['component']))
                {{-- Custom Livewire component (e.g. CRUD-style groups like Redirect) --}}
                @livewire($currentGroup['component'], [], key('settings-component-' . $group))
            @else
                <form wire:submit="save" class="space-y-6">
                    @php
                        // Group fields by `section` so the page reads as multiple cards.
                        $sections = collect($fields)->groupBy(fn($f) => $f['section'] ?? 'General');
                    @endphp

                    @foreach($sections as $sectionName => $sectionFields)
                        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 md:p-8 shadow-sm">
                            <h2 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC] mb-6">{{ $sectionName }}</h2>

                            <div class="space-y-5">
                                @foreach($sectionFields as $field)
                                    @include('livewire.admin.settings.partials.field', ['field' => $field])
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="flex flex-wrap items-center justify-between gap-3 sticky bottom-0 bg-gradient-to-t from-[#F4F5F6] dark:from-[#0B0B0B] via-[#F4F5F6]/95 dark:via-[#0B0B0B]/95 to-transparent py-4 -mx-4 px-4 z-10">
                        {{-- Actions registered for this group --}}
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($currentGroup['actions'] ?? [] as $i => $action)
                                @php
                                    $color = $action['color'] ?? 'gray';
                                    $btn = match ($color) {
                                        'blue'   => 'bg-blue-50 dark:bg-blue-500/10 text-[#2563EB] hover:bg-blue-100 dark:hover:bg-blue-500/20',
                                        'green'  => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20',
                                        'red'    => 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20',
                                        default  => 'bg-gray-100 dark:bg-[#272B30] text-[#6F767E] hover:bg-gray-200 dark:hover:bg-[#333]',
                                    };
                                @endphp
                                <button
                                    type="button"
                                    wire:click="runAction({{ $i }})"
                                    wire:loading.attr="disabled"
                                    wire:target="runAction({{ $i }})"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition disabled:opacity-50 {{ $btn }}"
                                >
                                    <span wire:loading.remove wire:target="runAction({{ $i }})" class="material-symbols-outlined text-[18px]">{{ $action['icon'] ?? 'bolt' }}</span>
                                    <span wire:loading wire:target="runAction({{ $i }})" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                                    {{ $action['label'] }}
                                </button>
                            @endforeach
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#2563EB] text-white text-sm font-bold hover:bg-blue-700 transition-colors disabled:opacity-50 ml-auto"
                            wire:loading.attr="disabled"
                            wire:target="save"
                        >
                            <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px]">save</span>
                            <span wire:loading wire:target="save" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                            Save changes
                        </button>
                    </div>
                </form>
            @endif
        </main>
    </div>
</div>
