<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageAdminController extends Controller
{
    /**
     * List all Pages
     */
    public function index(Request $request)
    {
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $pages = Page::latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $pages,
        ]);
    }

    /**
     * Create a new Page
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'template' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'status' => 'nullable|string|in:draft,published,scheduled,archived',
            'published_at' => 'nullable|date',
            'translations' => 'nullable|array',
            'menu_order' => 'nullable|integer',
        ]);

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['template'] = $validated['template'] ?? 'default';
        $validated['status'] = $validated['status'] ?? 'published';
        $validated['author_id'] = auth()->id() ?? 1;

        $page = Page::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully.',
            'data' => $page,
        ], 201);
    }

    /**
     * Show single Page details with Blocks
     */
    public function show(int $id)
    {
        $page = Page::with(['blocks' => fn ($q) => $q->whereNull('parent_block_id')->with('childBlocks')])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $page,
        ]);
    }

    /**
     * Update an existing Page
     */
    public function update(Request $request, int $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:pages,slug,'.$page->id,
            'template' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'status' => 'nullable|string|in:draft,published,scheduled,archived',
            'published_at' => 'nullable|date',
            'translations' => 'nullable|array',
            'menu_order' => 'nullable|integer',
            'blocks' => 'nullable|array',
        ]);

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $page->update(collect($validated)->except('blocks')->toArray());

        if ($request->has('blocks') && is_array($request->blocks)) {
            foreach ($request->blocks as $index => $blockData) {
                if (empty($blockData['name'])) {
                    continue;
                }
                $existingBlock = PageBlock::where('page_id', $page->id)
                    ->where('name', $blockData['name'])
                    ->first();

                $value = $blockData['value'] ?? null;
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                $type = $blockData['type'] ?? ($existingBlock->type ?? 'text');
                if ($type === 'image') {
                    $type = 'media';
                }

                $blockPayload = [
                    'type' => $type,
                    'value' => $value,
                    'order' => $blockData['order'] ?? $existingBlock->order ?? $index,
                    'is_active' => $blockData['is_active'] ?? $existingBlock->is_active ?? true,
                ];

                if (isset($blockData['translations']) && is_array($blockData['translations'])) {
                    $blockPayload['translations'] = $blockData['translations'];
                }

                if ($existingBlock) {
                    $existingBlock->update($blockPayload);
                } else {
                    $blockPayload['page_id'] = $page->id;
                    $blockPayload['name'] = $blockData['name'];
                    $blockPayload['label'] = $blockData['label'] ?? Str::title(str_replace('_', ' ', $blockData['name']));
                    PageBlock::create($blockPayload);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully.',
            'data' => $page->fresh(['blocks']),
        ]);
    }

    /**
     * Delete a Page
     */
    public function destroy(int $id)
    {
        $page = Page::findOrFail($id);
        if ($page->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'System pages cannot be deleted.',
            ], 422);
        }

        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully.',
        ]);
    }

    /**
     * List Blocks for a Page
     */
    public function listBlocks(int $id)
    {
        $page = Page::findOrFail($id);
        $blocks = PageBlock::where('page_id', $page->id)
            ->whereNull('parent_block_id')
            ->with('childBlocks')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $blocks,
        ]);
    }

    /**
     * Create a PageBlock for a Page
     */
    public function storeBlock(Request $request, int $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'value' => 'nullable',
            'options' => 'nullable|array',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'translations' => 'nullable|array',
            'parent_block_id' => 'nullable|integer|exists:page_blocks,id',
        ]);

        $validated['page_id'] = $page->id;
        if (is_array($validated['value'] ?? null)) {
            $validated['value'] = json_encode($validated['value']);
        }

        $block = PageBlock::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page Block created successfully.',
            'data' => $block,
        ], 201);
    }

    /**
     * Update a PageBlock
     */
    public function updateBlock(Request $request, int $id, int $blockId)
    {
        $block = PageBlock::where('page_id', $id)->where('id', $blockId)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:50',
            'value' => 'nullable',
            'options' => 'nullable|array',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'translations' => 'nullable|array',
        ]);

        if (array_key_exists('value', $validated) && is_array($validated['value'])) {
            $validated['value'] = json_encode($validated['value']);
        }

        $block->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page Block updated successfully.',
            'data' => $block,
        ]);
    }

    /**
     * Delete a PageBlock
     */
    public function destroyBlock(int $id, int $blockId)
    {
        $block = PageBlock::where('page_id', $id)->where('id', $blockId)->firstOrFail();
        $block->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page Block deleted successfully.',
        ]);
    }
}
