@extends('layouts.admin')

@section('title', 'Authors Management')
@section('page-title', 'Authors')

@section('content')
<div class="space-y-6">
    @livewire('plugins.authors-manager')
</div>
@endsection
