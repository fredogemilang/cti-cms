@extends('layouts.admin')

@section('title', 'Import Posts')
@section('page-title', 'Import Posts')
@section('page-subtitle', 'Import blog posts, categories, tags, and media directly from WordPress')

@section('content')
<div class="space-y-6">
    @livewire('plugins.wordpress-migration')
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
