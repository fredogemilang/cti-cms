@extends('layouts.admin')

@section('title', 'Edit Custom Post Type')
@section('hide-title', true)

@section('content')
    <livewire:admin.cpt.cpt-form :id="$id" />
@endsection
