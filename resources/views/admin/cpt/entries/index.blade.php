@extends('layouts.admin')

@section('title', $postType->plural_label)
@section('page-title', $postType->plural_label)
@if($postType->description)
@section('page-subtitle', $postType->description)
@endif

@section('page-actions')
    <x-admin.ui.button 
        href="{{ route('admin.cpt.entries.create', $postType->slug) }}" 
        variant="primary"
        class="h-12 px-6 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20"
    >
        <span class="material-symbols-outlined text-lg mr-2">add</span>
        Add {{ $postType->singular_label }}
    </x-admin.ui.button>
@endsection

@section('content')
    <livewire:admin.cpt.entries.entries-table :post-type="$postType" />
@endsection

