@extends('layouts.admin')

@section('title', 'Forms Management')
@section('page-title', 'All Forms')

@section('page-actions')
    @can('forms.create')
    <x-admin.ui.button 
        href="{{ route('admin.forms.create') }}" 
        variant="primary"
        class="h-12 px-6 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20"
    >
        <span class="material-symbols-outlined text-lg mr-2">add</span>
        Create Form
    </x-admin.ui.button>
    @endcan
@endsection

@section('content')
<div class="space-y-6">
    @livewire('admin.forms-table')
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
