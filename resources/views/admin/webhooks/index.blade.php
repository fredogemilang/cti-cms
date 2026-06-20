@extends('layouts.admin')

@section('title', 'Webhooks')
@section('page-title', 'Webhooks')

@section('content')
    <div class="px-6 pb-6 md:px-10 md:pb-10">
        @livewire('admin.webhooks.index')
    </div>
@endsection
