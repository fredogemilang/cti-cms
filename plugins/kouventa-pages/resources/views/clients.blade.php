@extends('layouts.admin')

@section('title', 'Clients — They Trusted Us')

@section('content')
    <div class="space-y-6">
        @livewire('plugins.kouventa-pages.client-manager')
    </div>
@endsection

