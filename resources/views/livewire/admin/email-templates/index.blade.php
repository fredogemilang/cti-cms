<div class="bg-white border border-gray-200 rounded-lg">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
                <th class="p-3">Name</th>
                <th class="p-3">Key</th>
                <th class="p-3">Subject</th>
                <th class="p-3 w-24">System</th>
                <th class="p-3 w-24">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($templates as $t)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3 font-medium">{{ $t->name }}</td>
                    <td class="p-3 font-mono text-xs text-gray-600">{{ $t->key_name }}</td>
                    <td class="p-3 text-gray-700 truncate max-w-xs">{{ $t->subject }}</td>
                    <td class="p-3">
                        @if ($t->is_system) <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">system</span> @endif
                    </td>
                    <td class="p-3">
                        <a href="{{ route('admin.email-templates.edit', $t->id) }}" class="text-blue-600 hover:underline">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-gray-500">No email templates. Run <code>php artisan db:seed --class=EmailTemplateSeeder</code>.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
