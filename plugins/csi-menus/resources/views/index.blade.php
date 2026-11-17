@extends('layouts.admin')

@section('title', 'Navigation Menus')
@section('page-title', 'Navigation Menus')
@section('page-subtitle', 'Manage frontend Header, Mega Menu, and Footer navigation links (EN / ID)')

@section('content')
    <div class="space-y-6">
        @livewire('csi-menus.manager')
    </div>
@endsection
