<?php

namespace Tests\Unit\Services;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\User;
use App\Services\ContentTextExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentTextExtractorTest extends TestCase
{
    use RefreshDatabase;

    protected ContentTextExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = new ContentTextExtractor;
    }

    public function test_clean_text_strips_html_and_collapses_whitespace(): void
    {
        $dirty = "<p>Hello   <strong>World!</strong> &amp;   welcome to &quot;CTI&quot;.</p>\n\n<div>More   text.</div>";
        $clean = $this->extractor->cleanText($dirty);

        $this->assertEquals('Hello World! & welcome to "CTI". More text.', $clean);
    }

    public function test_flatten_to_text_flattens_nested_arrays_and_skips_media_keys(): void
    {
        $data = [
            'hero_title' => 'Enterprise Cloud',
            'hero_icon' => 'cloud_upload',
            'hero_image' => 'uploads/hero.webp',
            'features' => [
                ['name' => 'Scalable', 'description' => 'Grows with demand', 'icon' => 'server'],
                ['name' => 'Secure', 'description' => 'End-to-end encryption', 'image' => 'uploads/shield.png'],
            ],
        ];

        $flattened = $this->extractor->flattenToText($data);

        $this->assertStringContainsString('Enterprise Cloud', $flattened);
        $this->assertStringContainsString('Scalable', $flattened);
        $this->assertStringContainsString('Grows with demand', $flattened);
        $this->assertStringNotContainsString('cloud_upload', $flattened);
        $this->assertStringNotContainsString('uploads/hero.webp', $flattened);
        $this->assertStringNotContainsString('uploads/shield.png', $flattened);
    }

    public function test_extracts_page_with_blocks_and_translations(): void
    {
        $user = User::factory()->create();

        $page = Page::create([
            'title' => 'About Our Cloud',
            'slug' => 'about-our-cloud',
            'status' => 'published',
            'author_id' => $user->id,
            'translations' => [
                'id' => [
                    'title' => 'Tentang Cloud Kami',
                ],
            ],
        ]);

        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'headline',
            'type' => 'text',
            'value' => 'Accelerate digital transformation.',
            'translations' => [
                'id' => ['value' => 'Percepat transformasi digital.'],
            ],
            'is_active' => true,
        ]);

        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'summary_card',
            'type' => 'card',
            'value' => [
                'title' => 'Global Infrastructure',
                'description' => 'Worldwide edge nodes.',
                'icon' => 'globe',
            ],
            'translations' => [
                'id' => [
                    'value' => [
                        'title' => 'Infrastruktur Global',
                        'description' => 'Node tepi di seluruh dunia.',
                    ],
                ],
            ],
            'is_active' => true,
        ]);

        // Default locale (en)
        $enData = $this->extractor->extract($page, 'en');
        $this->assertEquals('About Our Cloud', $enData['title']);
        $this->assertStringContainsString('Accelerate digital transformation.', $enData['body']);
        $this->assertStringContainsString('Global Infrastructure', $enData['body']);
        $this->assertStringContainsString('Worldwide edge nodes.', $enData['body']);

        // Indonesian locale (id)
        $idData = $this->extractor->extract($page, 'id');
        $this->assertEquals('Tentang Cloud Kami', $idData['title']);
        $this->assertStringContainsString('Percepat transformasi digital.', $idData['body']);
        $this->assertStringContainsString('Infrastruktur Global', $idData['body']);
        $this->assertStringContainsString('Node tepi di seluruh dunia.', $idData['body']);
    }

    public function test_extracts_cpt_entry_with_meta(): void
    {
        $user = User::factory()->create();

        $cpt = CustomPostType::create([
            'name' => 'Products',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'slug' => 'products',
            'is_active' => true,
            'publicly_queryable' => true,
        ]);

        $entry = CptEntry::create([
            'post_type_id' => $cpt->id,
            'author_id' => $user->id,
            'title' => 'Cloud Gateway',
            'slug' => 'cloud-gateway',
            'content' => '<p>High throughput gateway service.</p>',
            'excerpt' => 'Fast and reliable gateway.',
            'status' => 'published',
            'meta' => [
                'pricing' => 'Pay as you go',
                'featured_image' => 'uploads/gateway.webp',
                'specs' => [
                    'bandwidth' => '100 Gbps',
                    'sla' => '99.99%',
                ],
            ],
        ]);

        $extracted = $this->extractor->extract($entry, 'en');
        $this->assertEquals('Cloud Gateway', $extracted['title']);
        $this->assertEquals('Fast and reliable gateway.', $extracted['excerpt']);
        $this->assertStringContainsString('High throughput gateway service.', $extracted['body']);
        $this->assertStringContainsString('Pay as you go', $extracted['body']);
        $this->assertStringContainsString('100 Gbps', $extracted['body']);
        $this->assertStringNotContainsString('uploads/gateway.webp', $extracted['body']);
    }
}
