@extends('layouts.admin')

@section('title', 'Partner Reward Catalog')
@section('page-title', 'Partner Reward Catalog')
@section('page-subtitle', 'Upload and replace the single active PDF catalog rendered as an interactive 3D flipbook')

@section('content')
    <div class="space-y-6">
        @livewire('partner-reward-catalog.manager')
    </div>
@endsection
