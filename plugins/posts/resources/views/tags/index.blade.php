@extends('layouts.admin')

@section('title', 'Post Tags')
@section('page-title', 'Post Tags')
@section('page-subtitle', 'Organize your posts with tags')

@section('content')
<div class="flex flex-col gap-6">

    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] shadow-sm overflow-hidden">
        <div class="p-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-[#272B30] mb-6">
                <span class="material-symbols-outlined text-4xl text-[#6F767E]">label</span>
            </div>
            <h3 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC] mb-2">No tags yet</h3>
            <p class="text-[#6F767E] max-w-md mx-auto">Create tags to help readers find related content.</p>
        </div>
    </div>
</div>
@endsection
