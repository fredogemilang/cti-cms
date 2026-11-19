@extends('layouts.admin')

@section('title', 'Post Authors')
@section('page-title', 'Post Authors')
@section('page-subtitle', 'Manage authors for your blog posts')

@section('content')
<div class="space-y-6">
    @livewire('plugins.authors-manager')
</div>
@endsection
