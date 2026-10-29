@extends('layouts.admin')

@section('title', 'Kouventa Interactive Pages')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC]">Kouventa Interactive Pages</h1>
                <p class="text-sm text-[#6F767E] mt-1">Manage complex product, solutions, and pricing pages with structured sections and bilingual support.</p>
            </div>
        </div>

        @livewire('plugins.kouventa-pages.page-index')
    </div>
@endsection

