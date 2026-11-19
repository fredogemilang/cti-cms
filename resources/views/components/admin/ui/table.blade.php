@props([
    'loading' => false,
    'thead' => null
])

<div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl shadow-sm overflow-hidden relative">
    @if($loading)
    <div class="absolute top-0 left-0 right-0 h-1 z-20 overflow-hidden">
        <div class="h-full bg-[#2563EB] animate-indeterminate origin-left"></div>
    </div>
    @endif
    <div class="overflow-x-auto no-scrollbar">
        <table class="w-full text-left border-collapse">
            @if($thead)
            <thead>
                <tr class="bg-gray-50/50 dark:bg-[#0B0B0B]/20 border-b border-gray-100 dark:border-[#272B30]">
                    {{ $thead }}
                </tr>
            </thead>
            @endif
            <tbody class="divide-y divide-gray-50 dark:divide-[#272B30]/30 transition-opacity duration-200 {{ $loading ? 'opacity-50 pointer-events-none' : '' }}">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
