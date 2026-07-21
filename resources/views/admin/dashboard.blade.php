@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview & summary of your site')

@section('content')
<div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
    <div class="lg:col-span-3 space-y-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Users -->
            <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-6 shadow-sm flex flex-col justify-between border border-gray-200 dark:border-[#272B30]">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    @if($stats['users_change'] > 0)
                        <span class="text-xs font-bold text-[#83BF6E] bg-[#83BF6E]/15 px-2.5 py-1 rounded-lg">+{{ $stats['users_change'] }} / 7d</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-bold text-[#6F767E] uppercase tracking-wider mb-1">Total Users</p>
                    <p class="text-3xl font-bold text-[#111827] dark:text-[#FCFCFC]">{{ number_format($stats['total_users']) }}</p>
                </div>
            </div>

            <!-- Published Pages -->
            <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-6 shadow-sm flex flex-col justify-between border border-gray-200 dark:border-[#272B30]">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 rounded-2xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                        <span class="material-symbols-outlined">description</span>
                    </div>
                    @if($stats['pages_change'] > 0)
                        <span class="text-xs font-bold text-[#83BF6E] bg-[#83BF6E]/15 px-2.5 py-1 rounded-lg">+{{ $stats['pages_change'] }} / 7d</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-bold text-[#6F767E] uppercase tracking-wider mb-1">Published Pages</p>
                    <p class="text-3xl font-bold text-[#111827] dark:text-[#FCFCFC]">{{ number_format($stats['total_pages']) }}</p>
                </div>
            </div>

            <!-- Form Submissions -->
            <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-6 shadow-sm flex flex-col justify-between border border-gray-200 dark:border-[#272B30]">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 rounded-2xl bg-orange-500/10 text-orange-500 flex items-center justify-center">
                        <span class="material-symbols-outlined">forum</span>
                    </div>
                    @if($stats['entries_change'] > 0)
                        <span class="text-xs font-bold text-[#83BF6E] bg-[#83BF6E]/15 px-2.5 py-1 rounded-lg">+{{ $stats['entries_change'] }} / 7d</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-bold text-[#6F767E] uppercase tracking-wider mb-1">Form Submissions</p>
                    <p class="text-3xl font-bold text-[#111827] dark:text-[#FCFCFC]">{{ number_format($stats['total_entries']) }}</p>
                </div>
            </div>
        </div>

        <!-- Content Activity Chart -->
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-8 shadow-sm border border-gray-200 dark:border-[#272B30]">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Content Activity</h2>
                    <p class="text-sm text-[#6F767E] mt-1">Pages, entries & submissions created — last 7 days</p>
                </div>
            </div>
            <div class="h-64 w-full flex items-end justify-between gap-4 pt-4">
                @foreach($performance as $data)
                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                    <div class="w-full bg-[#2563EB]/20 rounded-t-lg relative group min-h-[4px]" style="height: {{ $data['height'] }};">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-white dark:bg-[#1A1D1F] text-[#111827] dark:text-white text-[10px] px-2 py-1 rounded shadow-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                            {{ $data['value'] }} item{{ $data['value'] === 1 ? '' : 's' }}
                        </div>
                        <div class="w-full h-1 bg-[#2563EB] absolute top-0 rounded-full"></div>
                    </div>
                    <span class="text-xs font-medium text-[#6F767E]">{{ $data['day'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Content Activity -->
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-0 shadow-sm overflow-hidden border border-gray-200 dark:border-[#272B30]">
            <div class="p-8 border-b border-gray-200 dark:border-[#272B30] flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Recent Content Activity</h2>
                <button class="text-sm font-bold text-[#2563EB] hover:underline transition-all">View History</button>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-[#272B30]">
                @forelse($recentActivities as $activity)
                <div class="p-6 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition-colors">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ strtoupper(substr($activity['name'], 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">
                            {{ $activity['name'] }}
                            <span class="font-normal text-[#6F767E]">{{ $activity['action'] }}</span>
                            {{ $activity['target'] }}
                        </p>
                        <p class="text-xs text-[#6F767E] mt-0.5">{{ $activity['time_human'] }}</p>
                    </div>
                    <span class="text-xs font-bold {{ $activity['typeColor'] }} px-2 py-1 rounded shrink-0">{{ $activity['type'] }}</span>
                </div>
                @empty
                <div class="p-6 text-sm text-[#6F767E] text-center">No recent activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <aside class="space-y-8">
        <!-- Site Status -->
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-6 shadow-sm border border-gray-200 dark:border-[#272B30]">
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-6">Site Status</h2>
            <div class="flex items-center justify-between p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#111315]">
                <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full {{ $maintenance ? 'bg-orange-500' : 'bg-[#83BF6E]' }} animate-pulse"></div>
                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $maintenance ? 'Maintenance' : 'Online' }}</span>
                </div>
                <span class="text-xs font-medium text-[#6F767E]">{{ config('app.env') }}</span>
            </div>
            <p class="text-[11px] text-[#6F767E] mt-4 text-center">
                {{ $maintenance ? 'Site is in maintenance mode.' : 'Site is reachable to the public.' }}
            </p>
        </div>

        <!-- Quick Inquiries -->
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-6 shadow-sm border border-gray-200 dark:border-[#272B30]">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Recent Inquiries</h2>
                @if(count($inquiries) > 0)
                    <span class="flex h-6 min-w-6 px-2 items-center justify-center rounded-full bg-[#FF6A55] text-[10px] font-bold text-white">{{ count($inquiries) }}</span>
                @endif
            </div>
            <div class="space-y-4">
                @forelse($inquiries as $inquiry)
                <div class="p-4 rounded-2xl border border-gray-100 dark:border-[#272B30] hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition-all">
                    <p class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $inquiry['name'] }}</p>
                    <p class="text-xs text-[#6F767E] mt-1 truncate">{{ $inquiry['message'] }}</p>
                    <p class="text-[10px] text-[#6F767E] mt-2">{{ $inquiry['time'] }}</p>
                </div>
                @empty
                <p class="text-xs text-[#6F767E] text-center py-4">No form submissions yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Content Health -->
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-6 shadow-sm border border-gray-200 dark:border-[#272B30]">
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-6">Content Health</h2>
            <div class="space-y-6">
                @php
                    $altColor = $altPct >= 80 ? '#83BF6E' : ($altPct >= 50 ? 'orange' : '#FF6A55');
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-[#6F767E]">Media Alt Tags</span>
                        <span class="text-xs font-bold" style="color: {{ $altColor }}">{{ $altPct }}%</span>
                    </div>
                    <div class="h-2 w-full bg-[#F4F5F6] dark:bg-[#272B30] rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: {{ $altPct }}%; background-color: {{ $altColor }};"></div>
                    </div>
                    <p class="text-[10px] text-[#6F767E] mt-1">{{ $totalImages }} image{{ $totalImages === 1 ? '' : 's' }} total</p>
                </div>
                <div class="opacity-50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-[#6F767E]">SEO Score</span>
                        <span class="text-[10px] font-bold text-[#6F767E] uppercase">Soon</span>
                    </div>
                    <div class="h-2 w-full bg-[#F4F5F6] dark:bg-[#272B30] rounded-full overflow-hidden"></div>
                    <p class="text-[10px] text-[#6F767E] mt-1">Available after SEO settings module.</p>
                </div>
            </div>
        </div>
    </aside>
</div>
@endsection
