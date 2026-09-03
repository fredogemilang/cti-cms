@extends('layouts.admin')

@section('title', 'AI / MCP Settings')
@section('page-title', 'AI / MCP Settings')

@section('content')
    <div class="px-6 pb-6 md:px-10 md:pb-10">
        <p class="text-sm text-gray-600 dark:text-[#6F767E] mb-4">
            Manage MCP (Model Context Protocol) tokens for AI coding assistants and chatbot integrations.
            Connect tools like Cursor, Windsurf, or Antigravity to this CMS.
        </p>
        @livewire('admin.ai.mcp-settings')
    </div>
@endsection
