<div>
    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-3 mb-6 items-end">
        <div class="relative flex-1 w-full">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] z-10">search</span>
            <x-admin.ui.input
                name="search"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search description or action..."
                class="!pl-12 !py-2.5 !rounded-xl !h-12 text-sm !w-full"
            />
        </div>

        <div class="w-full lg:w-48">
            <x-admin.ui.select name="userFilter" wire:model.live="userFilter" class="!py-2.5 !h-12 text-sm">
                <option value="">All users</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </x-admin.ui.select>
        </div>

        <div class="w-full lg:w-48">
            <x-admin.ui.select name="actionFilter" wire:model.live="actionFilter" class="!py-2.5 !h-12 text-sm">
                <option value="">All actions</option>
                @foreach($actionGroups as $prefix => $label)
                    <option value="{{ $prefix }}">{{ $label }}</option>
                @endforeach
            </x-admin.ui.select>
        </div>

        <div class="w-full lg:w-40">
            <x-admin.ui.input type="date" name="dateFrom" wire:model.live="dateFrom" class="!py-2.5 !h-12 text-sm" title="From date" />
        </div>
        <div class="w-full lg:w-40">
            <x-admin.ui.input type="date" name="dateTo" wire:model.live="dateTo" class="!py-2.5 !h-12 text-sm" title="To date" />
        </div>

        @if($search || $userFilter || $actionFilter || $dateFrom || $dateTo)
            <x-admin.ui.button 
                wire:click="clearFilters" 
                variant="secondary"
                class="!h-12 !px-4 !rounded-xl text-sm whitespace-nowrap flex items-center gap-2"
            >
                <span class="material-symbols-outlined text-lg">close</span>
                Clear
            </x-admin.ui.button>
        @endif
    </div>

    {{-- Table --}}
    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header class="px-6">User</x-admin.ui.table-header>
            <x-admin.ui.table-header>Action</x-admin.ui.table-header>
            <x-admin.ui.table-header>Description</x-admin.ui.table-header>
            <x-admin.ui.table-header>When</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="w-12 px-6"></x-admin.ui.table-header>
        </x-slot:thead>

        @forelse($activities as $a)
            @php
                $actionParts = explode('.', $a->action);
                $module      = $actionParts[0] ?? 'system';
                $verb        = $actionParts[1] ?? '';
                $color = match (true) {
                    str_contains($a->action, 'created')  => 'bg-emerald-500/15 text-emerald-500',
                    str_contains($a->action, 'updated')  => 'bg-blue-500/15 text-blue-500',
                    str_contains($a->action, 'deleted')  => 'bg-red-500/15 text-red-500',
                    str_contains($a->action, 'login')    => 'bg-purple-500/15 text-purple-500',
                    str_contains($a->action, 'logout')   => 'bg-gray-500/15 text-gray-500',
                    str_contains($a->action, 'failed')   => 'bg-orange-500/15 text-orange-500',
                    default                              => 'bg-gray-500/15 text-gray-500',
                };
            @endphp
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell class="px-6">
                    @if($a->user)
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                @if($a->user->avatar)
                                    <img src="{{ asset('storage/' . $a->user->avatar) }}" alt="{{ $a->user->name }}" class="h-full w-full rounded-full object-cover">
                                @else
                                    {{ strtoupper(substr($a->user->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $a->user->name }}</p>
                                <p class="text-[10px] text-[#6F767E] truncate">{{ $a->user->email }}</p>
                            </div>
                        </div>
                    @else
                        <span class="text-xs text-[#6F767E] italic">System</span>
                    @endif
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded {{ $color }}">{{ $a->action }}</span>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-[#111827] dark:text-[#FCFCFC] max-w-md">
                    <p class="truncate">{{ $a->description ?: '—' }}</p>
                    @if($a->subject_type && $a->subject_id)
                        <p class="text-[10px] text-[#6F767E] mt-0.5 font-mono">
                            {{ class_basename($a->subject_type) }}#{{ $a->subject_id }}
                        </p>
                    @endif
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-xs text-[#6F767E] whitespace-nowrap">
                    <span title="{{ $a->created_at }}">{{ $a->created_at->diffForHumans() }}</span>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-6">
                    @if($a->properties)
                        <button wire:click="toggleExpand({{ $a->id }})" class="p-2 rounded-lg text-[#6F767E] hover:text-[#2563EB] hover:bg-blue-500/10 transition" title="View details">
                            <span class="material-symbols-outlined text-[18px]">{{ $expandedId === $a->id ? 'expand_less' : 'expand_more' }}</span>
                        </button>
                    @endif
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
            @if($expandedId === $a->id && $a->properties)
                <tr class="bg-gray-50/50 dark:bg-[#0B0B0B]/10 border-t border-gray-100 dark:border-[#272B30]">
                    <td colspan="5" class="px-6 py-4">
                        <div class="text-xs text-[#6F767E] mb-2 font-bold uppercase tracking-wider">Properties</div>
                        <pre class="text-[11px] font-mono text-[#111827] dark:text-[#FCFCFC] bg-white dark:bg-[#1A1A1A] p-4 rounded-xl overflow-x-auto border border-gray-200 dark:border-[#272B30]">{{ json_encode($a->properties, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre>
                        @if($a->ip_address || $a->user_agent)
                            <div class="mt-3 flex flex-wrap gap-4 text-[11px] text-[#6F767E] font-medium">
                                @if($a->ip_address)<span><strong>IP:</strong> {{ $a->ip_address }}</span>@endif
                                @if($a->user_agent)<span class="truncate max-w-lg" title="{{ $a->user_agent }}"><strong>UA:</strong> {{ $a->user_agent }}</span>@endif
                            </div>
                        @endif
                    </td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="5" class="p-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-4xl text-[#6F767E]">history</span>
                        <p class="text-sm font-medium text-[#6F767E]">No activities match your filters</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.ui.table>

    @if($activities->hasPages())
    <div class="mt-6 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 flex items-center justify-between shadow-sm">
        <p class="text-sm font-medium text-[#6F767E]">
            Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} activities
        </p>
        <div class="flex items-center gap-2">
            @if($activities->onFirstPage())
            <button disabled
                class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed border border-gray-200 dark:border-[#272B30]">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            @else
            <button wire:click="previousPage"
                class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            @endif

            @foreach($activities->getUrlRange(max(1, $activities->currentPage() - 2), min($activities->lastPage(), $activities->currentPage() + 2)) as $page => $url)
                @if($page == $activities->currentPage())
                <button class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20">{{ $page }}</button>
                @else
                <button wire:click="gotoPage({{ $page }})" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</button>
                @endif
            @endforeach

            @if($activities->hasMorePages())
            <button wire:click="nextPage"
                class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
            @else
            <button disabled
                class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed border border-gray-200 dark:border-[#272B30]">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
            @endif
        </div>
    </div>
    @endif
</div>

