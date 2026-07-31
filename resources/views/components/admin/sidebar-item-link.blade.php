{{-- Simple link menu item (no children) --}}
<li>
    <a wire:navigate
       class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 nav-item overflow-hidden {{ $isActive ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
       href="{{ $href }}">
        <span class="material-symbols-outlined shrink-0">{{ $icon }}</span>
        <span class="font-semibold text-[15px] sidebar-text">{{ $title }}</span>
        <span class="sidebar-tooltip">{{ $title }}</span>
    </a>
</li>
