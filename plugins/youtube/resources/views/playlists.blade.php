@extends('layouts.admin')
@section('title', 'YouTube Playlists')
@section('page-title', 'YouTube Playlists')
@section('content')
<div class="space-y-6">
    @livewire('plugins.youtube-playlists')
</div>
@endsection
