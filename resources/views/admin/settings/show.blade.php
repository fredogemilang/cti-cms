@extends('layouts.admin')

@section('content')
    <livewire:admin.settings.settings-page :group="$group" :key="'settings-' . $group" />
@endsection
