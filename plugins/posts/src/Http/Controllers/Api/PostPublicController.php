<?php

namespace Plugins\Posts\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugins\Posts\Models\Category;
use Plugins\Posts\Models\Post;

class PostPublicController extends Controller
{
    /**
     * Get published blog posts list
     */
    public function index(Request $request)
    {
        $perPage = min(100, max(1, (int) $request->query('per_page', 12)));

        $q = Post::with(['category', 'author'])
            ->where('status', 'published')
            ->where('published_at', '<=', now());

        if ($catSlug = $request->query('category')) {
            $q->whereHas('category', fn ($c) => $c->where('slug', $catSlug));
        }

        if ($search = $request->query('q')) {
            $q->where('title', 'like', "%{$search}%");
        }

        $posts = $q->latest('published_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    /**
     * Get single published blog post detail
     */
    public function show(string $slug)
    {
        $post = Post::with(['category', 'author'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    /**
     * List blog categories
     */
    public function categories()
    {
        $categories = Category::withCount('posts')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
