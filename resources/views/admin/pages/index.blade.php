@extends('layouts.admin')

@section('title', 'Pages')
@section('page-title', 'Pages')
@section('page-subtitle', 'Manage and organize your website structure and content.')

@section('page-actions')
    <x-admin.ui.button 
        href="{{ route('admin.pages.create') }}" 
        variant="primary"
        class="h-12 px-6 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20"
    >
        <span class="material-symbols-outlined text-lg mr-2">add</span>
        Add New Page
    </x-admin.ui.button>
@endsection

@section('content')
    <livewire:admin.pages.pages-table />
@endsection
