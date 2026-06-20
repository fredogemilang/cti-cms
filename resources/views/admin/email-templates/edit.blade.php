@extends('layouts.admin')

@section('title', 'Edit Email Template')
@section('page-title', 'Edit Email Template')

@section('content')
    <div class="px-6 pb-6 md:px-10 md:pb-10">
        @livewire('admin.email-templates.edit', ['id' => $id])
    </div>
@endsection
