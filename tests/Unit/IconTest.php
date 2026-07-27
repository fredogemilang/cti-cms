<?php

namespace Tests\Unit;

use App\Services\IconLibraryService;
use Tests\TestCase;

class IconTest extends TestCase
{
    public function test_icon_library_service_loads_libraries()
    {
        $service = new IconLibraryService;
        $libraries = $service->getLibraries();

        $this->assertArrayHasKey('lucide', $libraries);
        $this->assertEquals('Lucide Icons', $libraries['lucide']['name']);
        $this->assertNotEmpty($libraries['lucide']['icons']);
    }

    public function test_search_icons_returns_matching_results()
    {
        $service = new IconLibraryService;
        $results = $service->searchIcons('shield');

        $this->assertNotEmpty($results);
        $this->assertContains('lucide:shield', array_column($results, 'key'));
    }

    public function test_render_icon_helper_outputs_svg()
    {
        $svg = render_icon('lucide:shield', 'w-6 h-6');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('class="w-6 h-6"', $svg);
        $this->assertStringContainsString('</svg>', $svg);
    }
}
