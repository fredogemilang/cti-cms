@extends('layouts.admin')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')
    <livewire:admin.activity-log.activity-table />
@endsection
