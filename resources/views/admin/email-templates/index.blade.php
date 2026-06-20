@extends('layouts.admin')

@section('title', 'Email Templates')
@section('page-title', 'Email Templates')

@section('content')
    <div class="px-6 pb-6 md:px-10 md:pb-10">
        @livewire('admin.email-templates.index')
    </div>
@endsection
