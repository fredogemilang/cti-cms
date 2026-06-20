@extends('layouts.admin')

@section('title', 'API Tokens')
@section('page-title', 'API Tokens')

@section('content')
    <div class="px-6 pb-6 md:px-10 md:pb-10">
        <p class="text-sm text-gray-600 mb-4">
            Tokens authenticate API clients. Use with <code class="bg-gray-100 px-1 py-0.5 rounded">Authorization: Bearer &lt;token&gt;</code>.
            See spec at <a class="text-blue-600 hover:underline" href="{{ url('/api/v1/openapi.json') }}">/api/v1/openapi.json</a>.
        </p>
        @livewire('admin.api-tokens.index')
    </div>
@endsection
