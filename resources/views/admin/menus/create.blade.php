@extends('layouts.admin')

@section('title', 'Create Menu Item')
@section('page-title', 'Create Menu Item')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.menus.index') }}" wire:navigate class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-[#FCFCFC] font-medium transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Menus
    </a>

    <!-- Form Card -->
    <x-admin.ui.card padding="p-8">
        <form action="{{ route('admin.menus.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Title -->
            <x-admin.ui.input name="title" label="Menu Title" :value="old('title')" required />

            <!-- Parent Menu -->
            <x-admin.ui.select name="parent_id" label="Parent Menu (Optional)">
                <option value="">None (Top Level)</option>
                @foreach($parentMenus as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                        {{ $parent->title }}
                    </option>
                @endforeach
            </x-admin.ui.select>

            <!-- Icon -->
            <div>
                <x-admin.ui.input name="icon" label="Icon Class (Optional)" :value="old('icon')" placeholder="e.g., fas fa-home, heroicon-o-home" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Font Awesome or Heroicon class name</p>
            </div>

            <!-- Route -->
            <x-admin.ui.input name="route" label="Route Name (Optional)" :value="old('route')" placeholder="e.g., admin.users.index" />

            <!-- Permission -->
            <x-admin.ui.select name="permission" label="Required Permission (Optional)">
                <option value="">None (Visible to all)</option>
                @foreach($permissions as $perm)
                    <option value="{{ $perm->name }}" {{ old('permission') == $perm->name ? 'selected' : '' }}>
                        {{ $perm->name }} - {{ $perm->description }}
                    </option>
                @endforeach
            </x-admin.ui.select>

            <!-- Order -->
            <div>
                <x-admin.ui.input type="number" name="order" label="Display Order" :value="old('order', 0)" required min="0" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
            </div>

            <!-- Is Active -->
            <x-admin.ui.checkbox name="is_active" label="Active" :checked="old('is_active', true)" description="Show this menu item in navigation" />

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-[#272B30]">
                <a href="{{ route('admin.menus.index') }}" wire:navigate class="px-6 py-3 text-gray-700 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-100 dark:hover:bg-[#272B30] transition border border-transparent dark:border-[#272B30]">
                    Cancel
                </a>
                <x-admin.ui.button type="submit" variant="primary">
                    Create Menu Item
                </x-admin.ui.button>
            </div>
        </form>
    </x-admin.ui.card>
</div>
</div>
@endsection
