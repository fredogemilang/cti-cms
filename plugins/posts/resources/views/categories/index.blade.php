@extends('layouts.admin')

@section('title', 'Post Categories')
@section('page-title', 'Post Categories')
@section('page-subtitle', 'Organize your posts with categories')

@section('content')
    @livewire('plugins.categories-manager')
@endsection
