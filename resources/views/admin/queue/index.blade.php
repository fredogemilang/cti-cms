@extends('layouts.admin')

@section('title', 'Queue')
@section('page-title', 'Job Queue')

@section('content')
    <div class="px-6 pb-6 md:px-10 md:pb-10">
        @livewire('admin.queue.jobs-table')
    </div>
@endsection
