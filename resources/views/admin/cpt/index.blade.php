@extends('layouts.admin')

@section('title', 'Custom Post Types')
@section('page-title', 'Custom Post Types')

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.cpt.wordpress-migration') }}" 
            class="flex items-center justify-center rounded-xl bg-purple-500 px-6 py-3 text-sm font-bold text-white hover:bg-purple-600 transition-all shadow-lg shadow-purple-500/20">
            <span class="material-symbols-outlined mr-2">cloud_download</span>
            <span>Import from WP</span>
        </a>
        <x-admin.ui.button 
            href="{{ route('admin.cpt.create') }}" 
            variant="primary"
            class="h-12 px-6 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20"
        >
            <span class="material-symbols-outlined text-lg mr-2">add</span>
            Add Post Type
        </x-admin.ui.button>
    </div>
@endsection

@section('content')
    <livewire:admin.cpt.cpt-table />
@endsection
