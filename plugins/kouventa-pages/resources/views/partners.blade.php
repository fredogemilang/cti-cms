@extends('layouts.admin')

@section('title', 'Logo Partners — Technology Partners')

@section('content')
    <div class="space-y-6">
        @livewire('plugins.kouventa-pages.partner-manager')
    </div>
@endsection

