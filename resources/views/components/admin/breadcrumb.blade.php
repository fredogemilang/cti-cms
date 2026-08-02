<nav class="flex items-center space-x-1 text-sm mb-2" aria-label="Breadcrumb">
    <a wire:navigate href="{{ route('admin.dashboard') }}" class="text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-colors">
        Dashboard
    </a>

    @foreach($items as $item)
        <span class="material-symbols-outlined text-[#6F767E] text-[18px]">chevron_right</span>

        @if($loop->last)
            <span class="text-[#111827] dark:text-[#FCFCFC] font-medium">{{ $item['title'] }}</span>
        @else
            <a wire:navigate href="{{ $item['url'] ?? '#' }}" class="text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-colors">
                {{ $item['title'] }}
            </a>
        @endif
    @endforeach
</nav>
