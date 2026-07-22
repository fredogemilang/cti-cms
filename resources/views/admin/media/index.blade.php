@extends('layouts.admin')

@section('title', 'Media Library')
@section('page-title', 'Media Library')
@section('page-subtitle', 'Manage your media files')

@section('page-actions')
    @can('media.upload')
    <x-admin.ui.button 
        type="button"
        onclick="Livewire.dispatch('open-upload-modal')"
        variant="primary"
        class="h-12 px-6 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20"
    >
        <span class="material-symbols-outlined text-lg mr-2">add</span>
        Upload Media
    </x-admin.ui.button>
    @endcan
@endsection

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Media Library Component --}}
    <livewire:admin.media-library />
</div>

{{-- Media Uploader Modal --}}
<livewire:admin.media-uploader />

{{-- Media Details Modal --}}
<livewire:admin.media-details />
@endsection
