@extends('layouts.admin')

@section('title', 'Trash')
@section('page-title', 'Trash')

@section('content')
    <div class="px-6 pb-6 md:px-10 md:pb-10">
        <p class="text-sm text-gray-600 mb-4">
            Deleted content. Items auto-purge after {{ setting('content_trash_retention_days', 30) }} days.
        </p>
        @livewire('admin.trash.trash-index')
    </div>
@endsection
