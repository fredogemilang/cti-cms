@extends('layouts.admin')

@section('title', 'Edit Page')
@section('hide-title', true)

@section('content')
    <livewire:admin.pages.page-form :id="$id" />
@endsection

