@extends('layouts.admin')

@section('title', 'Edit: ' . ($page->title ?? 'Kouventa Page'))

@section('content')
    <div class="w-full pb-16">
        @livewire('plugins.kouventa-pages.page-editor', ['pageId' => $id])
    </div>
@endsection
