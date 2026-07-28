@extends('layouts.admin')

@section('title', 'Menu Management')
@section('page-title', 'Menu Management')
@section('page-subtitle', 'Drag and drop items to customize the admin sidebar navigation order')

@section('content')
<div class="space-y-6" x-data="menuManager()">

    <!-- Action Bar & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-[#1A1A1A] p-5 rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 flex items-center justify-center font-bold shrink-0">
                <span class="material-symbols-outlined">reorder</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 dark:text-[#FCFCFC] text-base">Sidebar Order Customizer</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Drag any item up or down to reorder the sidebar. Your custom layout applies live across the panel.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <button 
                @click="resetOrder()" 
                type="button" 
                class="px-4 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset to Default
            </button>

            <button 
                @click="saveOrder()" 
                type="button" 
                :disabled="saving"
                class="px-5 py-2 text-xs font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-md transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50">
                <template x-if="saving">
                    <span class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
                </template>
                <template x-if="!saving">
                    <span class="material-symbols-outlined text-sm">save</span>
                </template>
                <span x-text="saving ? 'Saving...' : 'Save Order'"></span>
            </button>
        </div>
    </div>

    <!-- Notification Toast -->
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        x-cloak
        class="p-4 rounded-2xl border text-sm font-semibold flex items-center justify-between shadow-lg"
        :class="toast.type === 'success' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20'">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined" x-text="toast.type === 'success' ? 'check_circle' : 'error'"></span>
            <span x-text="toast.message"></span>
        </div>
        <button @click="toast.show = false" class="text-xs opacity-70 hover:opacity-100">✕</button>
    </div>

    <!-- Unified Menu Sortable Container -->
    <x-admin.ui.card padding="p-6">
        <div class="space-y-3" id="sortable-menu-list">
            @forelse($menus as $index => $item)
            @php
                $isCore = ($item['source'] ?? 'core') === 'core';
                $isCpt = ($item['source'] ?? '') === 'cpt';
                $isPlugin = str_starts_with($item['source'] ?? '', 'plugin:');
                $section = $item['section'] ?? ($isCpt ? 'CONTENT' : ($isPlugin ? 'PLUGINS' : 'SYSTEM'));
            @endphp
            
            <div 
                data-key="{{ $item['key'] ?? $item['source'] }}"
                class="sortable-item group bg-white/70 dark:bg-[#1A1A1A]/80 rounded-2xl border border-gray-200 dark:border-[#272B30] p-4 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-700 hover:shadow-md cursor-grab active:cursor-grabbing flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Left: Drag Handle, Position, Icon, Title, Section & Source Badges -->
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <!-- Drag Handle -->
                    <div class="text-gray-400 dark:text-gray-600 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors shrink-0">
                        <span class="material-symbols-outlined text-2xl select-none">drag_indicator</span>
                    </div>

                    <!-- Position Number Badge -->
                    <span class="pos-badge text-xs font-mono font-bold px-2 py-1 rounded-lg bg-gray-100 dark:bg-[#272B30] text-gray-500 dark:text-gray-400 shrink-0">
                        #{{ $index + 1 }}
                    </span>

                    <!-- Icon Container (Color-Coded) -->
                    @if($isCpt)
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 flex items-center justify-center font-bold shrink-0">
                            <span class="material-symbols-outlined text-xl">{{ $item['icon'] ?? 'article' }}</span>
                        </div>
                    @elseif($isPlugin)
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 border border-purple-500/20 flex items-center justify-center font-bold shrink-0">
                            <span class="material-symbols-outlined text-xl">{{ $item['icon'] ?? 'extension' }}</span>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 flex items-center justify-center font-bold shrink-0">
                            @if(!empty($item['icon']) && str_starts_with($item['icon'], 'fa-'))
                                <i class="{{ $item['icon'] }} text-base"></i>
                            @else
                                <span class="material-symbols-outlined text-xl">{{ $item['icon'] ?? 'widgets' }}</span>
                            @endif
                        </div>
                    @endif

                    <!-- Title & Badges -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-bold text-gray-900 dark:text-[#FCFCFC] text-base truncate">{{ $item['title'] }}</h4>
                            
                            <!-- Sidebar Section Badge -->
                            @if($section === 'MAIN')
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded-full">
                                    SECTION: {{ $section }}
                                </span>
                            @elseif($section === 'CONTENT')
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-full">
                                    SECTION: {{ $section }}
                                </span>
                            @elseif($section === 'PLUGINS')
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 rounded-full">
                                    SECTION: {{ $section }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 rounded-full">
                                    SECTION: {{ $section }}
                                </span>
                            @endif

                            <!-- Source Badge -->
                            @if($isCpt)
                                <span class="px-2.5 py-0.5 text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-full">
                                    Content (CPT)
                                </span>
                            @elseif($isPlugin)
                                <span class="px-2.5 py-0.5 text-xs font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 rounded-full">
                                    {{ $item['source_label'] ?? 'Plugin' }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 rounded-full">
                                    Core System
                                </span>
                            @endif
                        </div>

                        <!-- Sub-Items Pills Preview -->
                        @if(!empty($item['children']))
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sub-items:</span>
                            @foreach($item['children'] as $child)
                                <span class="px-2 py-0.5 text-[11px] font-medium bg-gray-100 dark:bg-[#272B30] text-gray-600 dark:text-gray-300 rounded-md">
                                    {{ $child['title'] }}
                                </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

            </div>
            @empty
            <div class="p-12 text-center text-gray-500 font-medium">No menu items available to order.</div>
            @endforelse
        </div>
    </x-admin.ui.card>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
function menuManager() {
    return {
        saving: false,
        toast: {
            show: false,
            type: 'success',
            message: ''
        },

        init() {
            const el = document.getElementById('sortable-menu-list');
            if (el) {
                new Sortable(el, {
                    animation: 150,
                    handle: '.sortable-item',
                    ghostClass: 'opacity-40',
                    onEnd: () => {
                        this.updatePositions();
                        this.saveOrder();
                    }
                });
            }
        },

        updatePositions() {
            const items = document.querySelectorAll('#sortable-menu-list .sortable-item');
            items.forEach((item, index) => {
                const badge = item.querySelector('.pos-badge');
                if (badge) {
                    badge.textContent = `#${index + 1}`;
                }
            });
        },

        getOrderedKeys() {
            const items = document.querySelectorAll('#sortable-menu-list .sortable-item');
            const keys = [];
            items.forEach(item => {
                const key = item.getAttribute('data-key');
                if (key) keys.push(key);
            });
            return keys;
        },

        async saveOrder() {
            this.saving = true;
            const keys = this.getOrderedKeys();

            try {
                const response = await fetch('{{ route("admin.menus.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: keys })
                });

                const data = await response.json();
                this.saving = false;

                if (response.ok) {
                    this.showToast('success', data.message || 'Menu layout order updated successfully.');
                } else {
                    this.showToast('danger', data.message || 'Failed to save menu order.');
                }
            } catch (err) {
                this.saving = false;
                this.showToast('danger', 'Network error while saving menu order.');
            }
        },

        async resetOrder() {
            if (!confirm('Are you sure you want to reset the admin menu layout to system defaults?')) {
                return;
            }

            this.saving = true;
            try {
                const response = await fetch('{{ route("admin.menus.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ reset: true })
                });

                const data = await response.json();
                this.saving = false;

                if (response.ok) {
                    this.showToast('success', data.message || 'Menu layout reset to default.');
                    setTimeout(() => window.location.reload(), 800);
                }
            } catch (err) {
                this.saving = false;
                this.showToast('danger', 'Failed to reset menu order.');
            }
        },

        showToast(type, message) {
            this.toast.type = type;
            this.toast.message = message;
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 4000);
        }
    }
}
</script>
@endpush
@endsection
