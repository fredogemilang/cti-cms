@extends('layouts.admin')

@section('title', 'Reorder ' . $postType->plural_label)
@section('page-title', 'Reorder ' . $postType->plural_label)

@section('content')
    <livewire:admin.cpt.entries.entries-reorder :post-type="$postType" />
@endsection
