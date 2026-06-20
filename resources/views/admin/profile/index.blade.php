@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'Profile')

@section('content')
    <div class="px-6 pb-6 md:px-10 md:pb-10 space-y-6">
        @livewire('admin.profile.profile-form')
        @livewire('admin.profile.two-factor-settings')
    </div>
@endsection
