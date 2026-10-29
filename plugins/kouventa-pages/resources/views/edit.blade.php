@extends('layouts.admin')

@section('title', 'Edit Page: ' . ($page->title ?? 'Kouventa Page'))
@section('hide-title', true)

@section('content')
    @livewire('plugins.kouventa-pages.page-editor', ['pageId' => $id])
@endsection

