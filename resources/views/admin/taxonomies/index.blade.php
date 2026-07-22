@extends('layouts.admin')

@section('title', 'Custom Taxonomies')
@section('page-title', 'Custom Taxonomies')

@section('page-actions')
    <x-admin.ui.button 
        href="{{ route('admin.taxonomies.create') }}" 
        variant="primary"
        class="h-12 px-6 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20"
    >
        <span class="material-symbols-outlined text-lg mr-2">add</span>
        Add Taxonomy
    </x-admin.ui.button>
@endsection

@section('content')
    <livewire:admin.taxonomies.taxonomy-table />
@endsection
