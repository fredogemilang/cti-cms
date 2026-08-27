<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PageBlockHelperTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function title_block_resolves_compound_array_and_fallback(): void
    {
        $page = $this->makePage('home');

        // Case 1: No block set -> returns default
        $result = $page->titleBlock('hero_title', ['prefix' => 'Speed Up Your', 'main' => 'Transformation Journey']);
        $this->assertSame(['prefix' => 'Speed Up Your', 'main' => 'Transformation Journey'], $result);

        // Case 2: Block set as array / JSON
        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'hero_title',
            'type' => 'title',
            'label' => 'Hero Title',
            'value' => json_encode(['prefix' => 'Accelerate', 'main' => 'Your Business']),
            'order' => 1,
            'is_active' => true,
        ]);

        $page = $page->fresh();
        $result = $page->titleBlock('hero_title', ['prefix' => 'Default Prefix', 'main' => 'Default Main']);
        $this->assertSame(['prefix' => 'Accelerate', 'main' => 'Your Business'], $result);

        // Case 3: Block set as plain string
        PageBlock::where('page_id', $page->id)->where('name', 'hero_title')->update(['value' => 'Simple Title']);
        $page = $page->fresh();
        $result = $page->titleBlock('hero_title', ['prefix' => 'Pref', 'main' => 'Default']);
        $this->assertSame(['prefix' => 'Pref', 'main' => 'Simple Title'], $result);
    }

    #[Test]
    public function button_block_resolves_compound_array_and_default_target(): void
    {
        $page = $this->makePage('home');

        // Case 1: No block set -> returns default with target _self
        $result = $page->buttonBlock('hero_cta', ['text' => 'Learn More', 'url' => '#learn']);
        $this->assertSame(['target' => '_self', 'text' => 'Learn More', 'url' => '#learn'], $result);

        // Case 2: Block set
        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'hero_cta',
            'type' => 'button',
            'label' => 'Hero CTA',
            'value' => json_encode(['text' => 'Contact Us', 'url' => '/contact', 'target' => '_blank']),
            'order' => 1,
            'is_active' => true,
        ]);

        $page = $page->fresh();
        $result = $page->buttonBlock('hero_cta', ['text' => 'Default', 'url' => '#']);
        $this->assertSame(['target' => '_blank', 'text' => 'Contact Us', 'url' => '/contact'], $result);
    }

    #[Test]
    public function card_block_resolves_compound_array_and_fallback(): void
    {
        $page = $this->makePage('home');

        // Case 1: No block set -> returns default
        $default = [
            'title' => 'Blog, News & Video',
            'description' => 'Explore insights',
            'image' => 'themes/cdt/assets/blog.jpg',
        ];
        $result = $page->cardBlock('blog_callout', $default);
        $this->assertSame($default, $result);

        // Case 2: Block set
        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'blog_callout',
            'type' => 'card',
            'label' => 'Blog Callout',
            'value' => json_encode(['title' => 'Custom Blog', 'image' => 'uploads/custom.jpg']),
            'order' => 1,
            'is_active' => true,
        ]);

        $page = $page->fresh();
        $result = $page->cardBlock('blog_callout', $default);
        $this->assertSame([
            'title' => 'Custom Blog',
            'description' => 'Explore insights',
            'image' => 'uploads/custom.jpg',
        ], $result);
    }

    #[Test]
    public function repeater_block_resolves_array_and_fallback(): void
    {
        $page = $this->makePage('home');

        // Case 1: No block set -> returns default
        $result = $page->repeaterBlock('items', [['title' => 'Default Item']]);
        $this->assertSame([['title' => 'Default Item']], $result);

        // Case 2: Block set
        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'items',
            'type' => 'repeater',
            'label' => 'Items',
            'value' => json_encode([['title' => 'Item 1'], ['title' => 'Item 2']]),
            'order' => 1,
            'is_active' => true,
        ]);

        $page = $page->fresh();
        $result = $page->repeaterBlock('items');
        $this->assertSame([['title' => 'Item 1'], ['title' => 'Item 2']], $result);
    }

    protected function makePage(string $slug): Page
    {
        $author = User::factory()->create();

        return Page::create([
            'title' => ucfirst($slug),
            'slug' => $slug,
            'status' => 'published',
            'author_id' => $author->id,
            'template' => 'default',
        ]);
    }
}
