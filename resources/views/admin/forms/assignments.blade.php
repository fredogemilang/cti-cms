@extends('layouts.admin')

@section('title', 'Form Assignments')
@section('page-title', 'Form Assignments')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#111827] dark:text-[#FCFCFC]">Theme Form Assignments</h2>
            <p class="text-[#6F767E] mt-1">Assign active forms to placeholders defined by the active theme ({{ $theme->name }})</p>
        </div>
    </div>

    @if(session('success'))
    <x-admin.ui.alert type="success">{{ session('success') }}</x-admin.ui.alert>
    @endif

    @if(session('error'))
    <x-admin.ui.alert type="danger">{{ session('error') }}</x-admin.ui.alert>
    @endif

    <x-admin.ui.card padding="p-6">
        @if(empty($placeholders))
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-600 text-5xl">layers_clear</span>
            <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-gray-100">No Placeholders Defined</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                The active theme <strong>{{ $theme->name }}</strong> does not define any form placeholders in its <code>theme.json</code> config file.
            </p>
        </div>
        @else
        <form action="{{ route('admin.forms.assignments.save') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="divide-y divide-gray-200 dark:divide-[#272B30]">
                @foreach($placeholders as $placeholder)
                <div class="py-6 first:pt-0 last:pb-0 flex flex-col md:flex-row gap-6 justify-between items-start md:items-center">
                    <div class="max-w-xl">
                        <h4 class="font-bold text-gray-900 dark:text-[#FCFCFC] text-[16px]">{{ $placeholder['label'] ?? $placeholder['key'] }}</h4>
                        @if(isset($placeholder['description']))
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $placeholder['description'] }}</p>
                        @endif
                        <span class="inline-block mt-2 font-mono text-[11px] bg-gray-100 dark:bg-[#0B0B0B] px-2 py-0.5 rounded text-gray-600 dark:text-gray-400">
                            @@form('{{ $placeholder['key'] }}')
                        </span>
                    </div>
                    
                    <div class="w-full md:w-80">
                        <x-admin.ui.select name="assignments[{{ $placeholder['key'] }}]">
                            <option value="">-- None / Disabled --</option>
                            @foreach($forms as $form)
                            <option 
                                value="{{ $form->id }}" 
                                {{ ($currentAssignments[$placeholder['key']] ?? null) == $form->id ? 'selected' : '' }}
                            >
                                {{ $form->name }}
                            </option>
                            @endforeach
                        </x-admin.ui.select>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="pt-6 border-t border-gray-200 dark:border-[#272B30] flex justify-end">
                <x-admin.ui.button type="submit" variant="primary">
                    Save Assignments
                </x-admin.ui.button>
            </div>
        </form>
        @endif
    </x-admin.ui.card>
</div>
@endsection
