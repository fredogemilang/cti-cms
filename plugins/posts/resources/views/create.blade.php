@extends('layouts.admin')

@section('title', 'Add New Post')
@section('hide-title', true)

@section('content')
    @livewire('plugins.post-form')
@endsection
