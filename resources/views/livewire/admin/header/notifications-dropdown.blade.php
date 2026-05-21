<div class="relative" x-data="{ open: @entangle('open') }" @click.away="open = false">
    <button
        @click="open = !open"
        type="button"
        class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-[#6F767E] shadow-sm hover:bg-gray-50 hover:text-[#111827] dark:bg-[#272B30] dark:text-[#6F767E] dark:hover:text-[#FCFCFC] transition-colors relative"
    >
        <span class="material-symbols-outlined text-[24px]">notifications</span>
        @if($unreadCount > 0)
            <span class="absolute top-2 right-2 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-[#FF6A55] text-[10px] font-bold text-white ring-2 ring-white dark:ring-[#0B0B0B]">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-cloak
        class="absolute right-0 mt-2 w-96 bg-white dark:bg-[#1A1A1A] rounded-2xl shadow-2xl z-50 border border-gray-200 dark:border-[#272B30] overflow-hidden"
    >
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-[#272B30]">
            <div>
                <p class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Notifications</p>
                <p class="text-xs text-[#6F767E]">{{ $unreadCount }} unread</p>
            </div>
            @if($unreadCount > 0)
                <button
                    wire:click="markAllAsRead"
                    class="text-xs font-bold text-[#2563EB] hover:underline"
                >
                    Mark all read
                </button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto divide-y divide-gray-100 dark:divide-[#272B30]">
            @forelse($recent as $n)
                @php
                    $data    = is_array($n->data) ? $n->data : (json_decode($n->data, true) ?? []);
                    $title   = $data['title']   ?? 'Notification';
                    $message = $data['message'] ?? '';
                    $icon    = $data['icon']    ?? 'info';
                    $url     = $data['url']     ?? null;
                    $color   = $data['color']   ?? 'blue';
                @endphp
                <div class="px-5 py-4 hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition flex items-start gap-3 {{ $n->read_at ? 'opacity-60' : '' }}">
                    <div class="h-9 w-9 shrink-0 rounded-full flex items-center justify-center
                        @switch($color)
                            @case('green') bg-emerald-500/15 text-emerald-500 @break
                            @case('red')   bg-red-500/15 text-red-500 @break
                            @case('orange') bg-orange-500/15 text-orange-500 @break
                            @default bg-blue-500/15 text-blue-500
                        @endswitch
                    ">
                        <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $title }}</p>
                        @if($message)
                            <p class="text-xs text-[#6F767E] mt-0.5 line-clamp-2">{{ $message }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-[10px] text-[#6F767E]">{{ $n->created_at->diffForHumans() }}</span>
                            @if($url)
                                <a href="{{ $url }}" wire:click="markAsRead('{{ $n->id }}')" class="text-[10px] font-bold text-[#2563EB] hover:underline">View</a>
                            @endif
                            @if(!$n->read_at)
                                <button wire:click="markAsRead('{{ $n->id }}')" class="text-[10px] font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]">Mark read</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <span class="material-symbols-outlined text-[40px] text-[#6F767E]">notifications_off</span>
                    <p class="text-sm font-medium text-[#6F767E] mt-2">No notifications yet</p>
                    <p class="text-xs text-[#6F767E] mt-0.5">System alerts will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
