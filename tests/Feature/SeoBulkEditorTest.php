<?php

namespace Tests\Feature;

use App\Livewire\Admin\Seo\SeoBulkEditor;
use App\Models\Page;
use App\Models\User;
use App\Services\SeoRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SeoBulkEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_editor_updates_seo_meta_and_renders_on_frontend(): void
    {
        $user = User::factory()->create();

        $page = Page::create([
            'title' => 'Original Page Title',
            'slug' => 'original-page',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        Livewire::test(SeoBulkEditor::class)
            ->set('titles.App\Models\Page_'.$page->id, 'Bulk Custom Title Override')
            ->set('descriptions.App\Models\Page_'.$page->id, 'Bulk Custom Meta Description')
            ->call('saveSeoRow', Page::class, $page->id);

        /** @var SeoRenderer $renderer */
        $renderer = app(SeoRenderer::class);
        $seo = $renderer->resolve($page);

        $this->assertStringContainsString('Bulk Custom Title Override', $seo['title']);
        $this->assertEquals('Bulk Custom Meta Description', $seo['description']);
    }
}
