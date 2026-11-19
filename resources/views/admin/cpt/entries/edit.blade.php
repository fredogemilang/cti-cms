@extends('layouts.admin')

@section('title', 'Edit ' . $postType->singular_label)
@section('hide-title', true)

@section('content')
    <livewire:admin.cpt.entries.entry-form :post-type="$postType" :id="$id" />
@endsection

