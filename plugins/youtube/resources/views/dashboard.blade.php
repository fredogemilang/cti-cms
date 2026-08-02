@extends('layouts.admin')
@section('title', 'YouTube Dashboard')
@section('page-title', 'YouTube Dashboard')
@section('content')
<div class="space-y-6">
    @livewire('plugins.youtube-dashboard')
</div>
@endsection
