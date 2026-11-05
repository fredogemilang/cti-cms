@extends('layouts.admin')

@section('title', 'YouTube Videos — See Kouventa in Action')

@section('content')
    <div class="space-y-6">
        @livewire('plugins.kouventa-pages.video-manager')
    </div>
@endsection
