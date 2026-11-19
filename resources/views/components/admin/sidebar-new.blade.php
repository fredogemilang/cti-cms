{{--
    Admin Sidebar Navigation
    
    All items are rendered generically from AdminMenuBuilder::getUnifiedMenuList().
    To add a new menu item, register it in AdminMenuBuilder — it will automatically
    appear here AND in the Menu Customizer.
    
    @see \App\Services\AdminMenuBuilder
    @see components/admin/sidebar-item.blade.php
--}}

@php
    $sidebarUnifiedList = app(\App\Services\AdminMenuBuilder::class)->getUnifiedMenuList();
    $currentSidebarSection = null;
@endphp

<ul class="space-y-1 pb-12">
    @foreach($sidebarUnifiedList as $item)
        @php
            $itemSec = $item['section'] ?? 'CONTENT';
        @endphp

        {{-- Output Section Header Banner when section changes --}}
        @if($itemSec !== $currentSidebarSection)
            @php $currentSidebarSection = $itemSec; @endphp
            <li class="px-4 pt-4 pb-2">
                <span class="text-[10px] font-bold text-[#6F767E] uppercase tracking-widest sidebar-text">{{ $currentSidebarSection }}</span>
            </li>
        @endif

        {{-- Generic Item Renderer --}}
        <x-admin.sidebar-item :item="$item" />

    @endforeach
</ul>
