<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Services\ResponsiveImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ResponsiveImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_responsive_image_service_builds_webp_for_media_model()
    {
        $user = User::factory()->create();

        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/test-logo.png',
            'webp_path' => 'media/test-logo.webp',
            'filename' => 'test-logo.png',
            'original_filename' => 'test-logo.png',
            'file_extension' => 'png',
            'mime_type' => 'image/png',
            'size' => 1024,
            'title' => 'Test Logo',
            'uploaded_by' => $user->id,
        ]);

        $service = app(ResponsiveImageService::class);
        $data = $service->build($media);

        $this->assertNotNull($data['webp_srcset']);
        $this->assertStringContainsString('media/test-logo.webp', $data['webp_srcset']);
    }

    public function test_x_image_renders_picture_with_webp_source_for_src_url()
    {
        $user = User::factory()->create();

        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/sample.png',
            'webp_path' => 'media/sample.webp',
            'filename' => 'sample.png',
            'original_filename' => 'sample.png',
            'file_extension' => 'png',
            'mime_type' => 'image/png',
            'size' => 2048,
            'title' => 'Sample Image',
            'uploaded_by' => $user->id,
        ]);

        $html = Blade::render('<x-image :src="$url" alt="Sample" />', [
            'url' => asset('storage/media/sample.png'),
        ]);

        $this->assertStringContainsString('<picture', $html);
        $this->assertStringContainsString('type="image/webp"', $html);
        $this->assertStringContainsString('media/sample.webp', $html);
    }

    public function test_x_image_handles_svg_without_srcset_or_collapsing()
    {
        $user = User::factory()->create();

        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/akamai.svg',
            'filename' => 'akamai.svg',
            'original_filename' => 'akamai.svg',
            'file_extension' => 'svg',
            'mime_type' => 'image/svg+xml',
            'size' => 3300,
            'title' => 'Akamai Logo',
            'uploaded_by' => $user->id,
        ]);

        $html = Blade::render('<x-image :src="$url" alt="Akamai" class="max-w-[200px] h-auto object-contain" />', [
            'url' => asset('storage/media/akamai.svg'),
        ]);

        $this->assertStringContainsString('class="contents"', $html);
        $this->assertStringNotContainsString('srcset=', $html);
        $this->assertStringContainsString('w-full max-w-[200px]', $html);
    }

    public function test_responsive_image_service_uses_active_request_host_for_local_public_disk(): void
    {
        $user = User::factory()->create();

        config([
            'app.url' => 'https://example.com',
            'filesystems.disks.public.url' => 'https://example.com/storage',
            'media.disk' => 'public',
        ]);

        $request = Request::create('https://www.example.com/');
        $this->app->instance('request', $request);
        app('url')->setRequest($request);

        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/hero.webp',
            'webp_path' => 'media/hero.webp',
            'filename' => 'hero.webp',
            'original_filename' => 'hero.webp',
            'file_extension' => 'webp',
            'mime_type' => 'image/webp',
            'size' => 2048,
            'width' => 1280,
            'height' => 625,
            'variants' => [
                'md' => ['path' => 'media/hero-md.webp', 'webp' => 'media/hero-md.webp', 'width' => 768, 'height' => 375],
            ],
            'title' => 'Hero',
            'uploaded_by' => $user->id,
        ]);

        $data = app(ResponsiveImageService::class)->build($media, 'lg');

        $this->assertStringStartsWith('https://www.example.com/storage/', $data['src']);
        $this->assertStringContainsString('https://www.example.com/storage/media/hero-md.webp', (string) $data['webp_srcset']);
        $this->assertStringNotContainsString('https://example.com/storage/', $data['src']);
        $this->assertStringNotContainsString('https://example.com/storage/', (string) $data['webp_srcset']);
    }
}
