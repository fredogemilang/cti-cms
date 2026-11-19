@extends('layouts.admin')

@section('title', 'Users Management')
@section('page-title', 'All Users')

@section('page-actions')
    @can('users.create')
    <x-admin.ui.button 
        href="{{ route('admin.users.create') }}" 
        variant="primary"
        class="h-12 px-6 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20"
    >
        <span class="material-symbols-outlined text-lg mr-2">add</span>
        Add New User
    </x-admin.ui.button>
    @endcan
@endsection

@section('content')
<div class="space-y-6">
    <!-- Livewire Users Table Component -->
    @livewire('admin.users-table')
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
