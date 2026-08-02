@extends('layouts.admin')

@section('title', 'Site Health & Diagnostic')
@section('page-title', 'Site Health')
@section('page-subtitle', 'System diagnostics, environment configuration, and PHP/MySQL health status')

@section('content')
<div class="space-y-8">
    {{-- System Status Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 shadow-sm">
            <div class="flex items-center gap-3 text-emerald-600 mb-2">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
                <span class="text-xs font-bold uppercase tracking-wider">PHP Version</span>
            </div>
            <div class="text-2xl font-extrabold text-[#111827] dark:text-[#FCFCFC]">{{ $phpVersion }}</div>
            <div class="text-xs text-[#6F767E] mt-1">PHP 8.3+ Supported</div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 shadow-sm">
            <div class="flex items-center gap-3 text-blue-600 mb-2">
                <span class="material-symbols-outlined text-2xl">database</span>
                <span class="text-xs font-bold uppercase tracking-wider">MySQL Version</span>
            </div>
            <div class="text-2xl font-extrabold text-[#111827] dark:text-[#FCFCFC]">{{ Str::before($dbVersion, '-') }}</div>
            <div class="text-xs text-[#6F767E] mt-1">{{ $dbVersion }}</div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 shadow-sm">
            <div class="flex items-center gap-3 text-purple-600 mb-2">
                <span class="material-symbols-outlined text-2xl">layers</span>
                <span class="text-xs font-bold uppercase tracking-wider">Laravel Framework</span>
            </div>
            <div class="text-2xl font-extrabold text-[#111827] dark:text-[#FCFCFC]">v{{ $laravelVersion }}</div>
            <div class="text-xs text-[#6F767E] mt-1">Laravel 13.x Core</div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 shadow-sm">
            <div class="flex items-center gap-3 text-amber-600 mb-2">
                <span class="material-symbols-outlined text-2xl">hard_drive</span>
                <span class="text-xs font-bold uppercase tracking-wider">Disk Storage</span>
            </div>
            <div class="text-2xl font-extrabold text-[#111827] dark:text-[#FCFCFC]">{{ $diskFreeMb }} GB</div>
            <div class="text-xs text-[#6F767E] mt-1">Free of {{ $diskTotalMb }} GB Total</div>
        </div>
    </div>

    {{-- PHP Environment Settings --}}
    <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 shadow-sm space-y-4">
        <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
            <span class="material-symbols-outlined text-[#2563EB]">tune</span>
            PHP Runtime Configuration
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div class="bg-gray-50 dark:bg-[#0B0B0B] p-4 rounded-xl border border-gray-100 dark:border-[#272B30]">
                <div class="text-xs text-[#6F767E]">Memory Limit</div>
                <div class="font-bold text-[#111827] dark:text-[#FCFCFC] mt-1">{{ $memoryLimit }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-[#0B0B0B] p-4 rounded-xl border border-gray-100 dark:border-[#272B30]">
                <div class="text-xs text-[#6F767E]">Max Execution Time</div>
                <div class="font-bold text-[#111827] dark:text-[#FCFCFC] mt-1">{{ $maxExecutionTime }}s</div>
            </div>
            <div class="bg-gray-50 dark:bg-[#0B0B0B] p-4 rounded-xl border border-gray-100 dark:border-[#272B30]">
                <div class="text-xs text-[#6F767E]">Max Upload Size</div>
                <div class="font-bold text-[#111827] dark:text-[#FCFCFC] mt-1">{{ $uploadMaxFilesize }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-[#0B0B0B] p-4 rounded-xl border border-gray-100 dark:border-[#272B30]">
                <div class="text-xs text-[#6F767E]">Post Max Size</div>
                <div class="font-bold text-[#111827] dark:text-[#FCFCFC] mt-1">{{ $postMaxSize }}</div>
            </div>
        </div>
    </div>

    {{-- PHP Required Extensions --}}
    <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 shadow-sm space-y-4">
        <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
            <span class="material-symbols-outlined text-[#2563EB]">extension</span>
            Required PHP Extensions
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
            @foreach($extensionsStatus as $ext => $loaded)
                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-[#272B30] {{ $loaded ? 'bg-emerald-50/50 dark:bg-emerald-950/20' : 'bg-red-50/50 dark:bg-red-950/20' }}">
                    <span class="text-xs font-bold uppercase text-[#111827] dark:text-[#FCFCFC]">{{ $ext }}</span>
                    <span class="material-symbols-outlined text-base {{ $loaded ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $loaded ? 'check_circle' : 'cancel' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
