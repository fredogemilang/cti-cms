@extends('layouts.admin')

@section('title', 'RMA Management')
@section('page-title', 'RMA Requests')

@section('content')
<div class="space-y-6">
    @livewire('plugins.rma-xyora.rma-table')
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
