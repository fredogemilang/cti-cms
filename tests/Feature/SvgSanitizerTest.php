<?php

namespace Tests\Feature;

use App\Mcp\Tools\Media\UploadMediaTool;
use App\Models\ApiToken;
use App\Models\Media;
use App\Models\User;
use App\Services\MediaService;
use App\Services\SvgSanitizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SvgSanitizerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('media.disk', 'public'));
    }

    #[Test]
    public function svg_sanitizer_service_strips_scripts_and_event_handlers(): void
    {
        $sanitizer = app(SvgSanitizerService::class);

        $dirtySvg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" onload="alert('xss-onload')">
            <circle cx="50" cy="50" r="40" fill="red" />
            <script>alert('xss-script');</script>
            <a href="javascript:alert('xss-link')">
                <text x="10" y="20">Click Me</text>
            </a>
            <foreignObject width="100" height="50">
                <body xmlns="http://www.w3.org/1999/xhtml">
                    <div>XSS embedded HTML</div>
                </body>
            </foreignObject>
        </svg>
        SVG;

        $this->assertTrue($sanitizer->hasThreats($dirtySvg));

        $cleanSvg = $sanitizer->clean($dirtySvg);
        $this->assertNotNull($cleanSvg);

        // Assert all malicious payloads are stripped
        $this->assertStringNotContainsString('<script', $cleanSvg);
        $this->assertStringNotContainsString('onload', $cleanSvg);
        $this->assertStringNotContainsString('javascript:', $cleanSvg);
        $this->assertStringNotContainsString('<foreignObject', $cleanSvg);

        // Assert legitimate visual elements are preserved
        $this->assertStringContainsString('<circle', $cleanSvg);
        $this->assertStringContainsString('fill="red"', $cleanSvg);
    }

    #[Test]
    public function svg_sanitizer_service_preserves_clean_svg(): void
    {
        $sanitizer = app(SvgSanitizerService::class);

        $cleanSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"><rect width="50" height="50" fill="blue"/></svg>';

        $this->assertFalse($sanitizer->hasThreats($cleanSvg));

        $result = $sanitizer->clean($cleanSvg);
        $this->assertNotNull($result);
        $this->assertStringContainsString('<rect', $result);
        $this->assertStringContainsString('fill="blue"', $result);
    }

    #[Test]
    public function svg_sanitizer_service_rejects_empty_or_unparseable_content(): void
    {
        $sanitizer = app(SvgSanitizerService::class);

        $this->assertNull($sanitizer->clean(''));
        $this->assertNull($sanitizer->clean('   '));
        $this->assertNull($sanitizer->clean('not an xml or svg at all'));
        $this->assertTrue($sanitizer->hasThreats('completely invalid content'));
    }

    #[Test]
    public function media_service_upload_sanitizes_svg_before_storing_to_disk(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $dirtySvgContent = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <path d="M10 10 H 90 V 90 H 10 Z" fill="green" />
            <script>alert('pwned');</script>
        </svg>
        SVG;

        $file = UploadedFile::fake()->createWithContent('test-logo.svg', $dirtySvgContent);

        $mediaService = app(MediaService::class);
        $media = $mediaService->upload($file);

        $this->assertInstanceOf(Media::class, $media);
        $this->assertSame('svg', $media->file_extension);

        // Verify stored file on disk
        $disk = Storage::disk(config('media.disk'));
        $storedContent = $disk->get($media->path);

        $this->assertStringNotContainsString('<script', $storedContent);
        $this->assertStringNotContainsString('alert', $storedContent);
        $this->assertStringContainsString('fill="green"', $storedContent);
    }

    #[Test]
    public function media_service_upload_rejects_malformed_svg_with_runtime_exception(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->createWithContent('malformed.svg', '<<<malformed xml content');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('SVG rejected: could not be parsed or sanitized.');

        $mediaService = app(MediaService::class);
        $mediaService->upload($file);
    }

    #[Test]
    public function media_sanitize_svg_command_dry_run_reports_without_modifying(): void
    {
        $user = User::factory()->create();
        $disk = Storage::disk(config('media.disk'));

        $dirtySvg = '<svg xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="5"/><script>alert(1)</script></svg>';
        $path = 'media/existing-dirty.svg';
        $disk->put($path, $dirtySvg);

        $media = Media::create([
            'filename' => 'existing-dirty.svg',
            'original_filename' => 'existing-dirty.svg',
            'mime_type' => 'image/svg+xml',
            'file_extension' => 'svg',
            'size' => strlen($dirtySvg),
            'path' => $path,
            'uploaded_by' => $user->id,
        ]);

        $this->artisan('media:sanitize-svg', ['--dry-run' => true])
            ->expectsOutputToContain('[DRY RUN]')
            ->expectsOutputToContain('<script> tag')
            ->assertExitCode(0);

        // Assert file on disk was NOT changed in dry-run mode
        $this->assertSame($dirtySvg, $disk->get($path));
        $this->assertStringContainsString('<script', $disk->get($path));
    }

    #[Test]
    public function media_sanitize_svg_command_sanitizes_disk_and_updates_database(): void
    {
        $user = User::factory()->create();
        $disk = Storage::disk(config('media.disk'));

        $dirtySvg = '<svg xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="5"/><script>alert(1)</script></svg>';
        $path = 'media/existing-dirty-real.svg';
        $disk->put($path, $dirtySvg);

        $media = Media::create([
            'filename' => 'existing-dirty-real.svg',
            'original_filename' => 'existing-dirty-real.svg',
            'mime_type' => 'image/svg+xml',
            'file_extension' => 'svg',
            'size' => strlen($dirtySvg),
            'path' => $path,
            'uploaded_by' => $user->id,
        ]);

        $this->artisan('media:sanitize-svg')
            ->expectsOutputToContain('Sanitizing SVG files')
            ->assertExitCode(0);

        // Assert file on disk is now clean
        $storedContent = $disk->get($path);
        $this->assertStringNotContainsString('<script', $storedContent);
        $this->assertStringContainsString('<circle', $storedContent);

        // Assert database record size was updated
        $this->assertSame(strlen($storedContent), $media->fresh()->size);
    }

    #[Test]
    public function media_sanitize_svg_command_leaves_threat_free_files_untouched(): void
    {
        $user = User::factory()->create();
        $disk = Storage::disk(config('media.disk'));

        // Clean SVG. The sanitizer still re-serializes it (XML declaration,
        // indentation, expanded self-closing tags), so the bytes differ — but
        // that is cosmetic and must NOT be reported or written as a threat.
        $cleanSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M1 1h5v5H1z"/></svg>';
        $path = 'media/already-clean.svg';
        $disk->put($path, $cleanSvg);

        $media = Media::create([
            'filename' => 'already-clean.svg',
            'original_filename' => 'already-clean.svg',
            'mime_type' => 'image/svg+xml',
            'file_extension' => 'svg',
            'size' => strlen($cleanSvg),
            'path' => $path,
            'uploaded_by' => $user->id,
        ]);

        // Sanity check: re-serialization really does change the bytes.
        $this->assertNotSame($cleanSvg, app(SvgSanitizerService::class)->clean($cleanSvg));

        $this->artisan('media:sanitize-svg')
            ->doesntExpectOutputToContain('Disallowed attributes')
            ->assertExitCode(0);

        // File must be byte-identical and the DB size untouched.
        $this->assertSame($cleanSvg, $disk->get($path));
        $this->assertSame(strlen($cleanSvg), $media->fresh()->size);
    }

    #[Test]
    public function media_sanitize_svg_command_force_rewrites_clean_files_without_claiming_threats(): void
    {
        $user = User::factory()->create();
        $disk = Storage::disk(config('media.disk'));

        $cleanSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M1 1h5v5H1z"/></svg>';
        $path = 'media/force-clean.svg';
        $disk->put($path, $cleanSvg);

        Media::create([
            'filename' => 'force-clean.svg',
            'original_filename' => 'force-clean.svg',
            'mime_type' => 'image/svg+xml',
            'file_extension' => 'svg',
            'size' => strlen($cleanSvg),
            'path' => $path,
            'uploaded_by' => $user->id,
        ]);

        $this->artisan('media:sanitize-svg', ['--force' => true])
            ->expectsOutputToContain('No threats found')
            ->assertExitCode(0);

        // --force does rewrite, but the file stays valid and threat-free.
        $this->assertNotSame($cleanSvg, $disk->get($path));
        $this->assertStringContainsString('<path', $disk->get($path));
    }

    #[Test]
    public function mcp_upload_tool_returns_error_response_for_unsanitizable_svg(): void
    {
        $user = User::factory()->create();
        $generated = ApiToken::generateFor($user, '[MCP] Editor', ApiToken::mcpEditorAbilities());
        request()->attributes->set('api_token', $generated['model']);

        try {
            $tool = new UploadMediaTool;

            $response = $tool->handle(new Request([
                'filename' => 'broken.svg',
                'base64_data' => base64_encode('<svg><unclosed'),
            ]));

            // Must be a clean tool-level error, not an escaped RuntimeException.
            $this->assertInstanceOf(Response::class, $response);
            $this->assertStringContainsString('SVG rejected', (string) $response->content());
        } finally {
            request()->attributes->remove('api_token');
        }
    }

    #[Test]
    public function static_storage_htaccess_contains_svg_csp_sandbox_directives(): void
    {
        $htaccessPath = storage_path('app/public/.htaccess');
        $this->assertFileExists($htaccessPath);

        $content = file_get_contents($htaccessPath);
        $this->assertStringContainsString('<FilesMatch "\.svg$">', $content);
        $this->assertStringContainsString("Content-Security-Policy \"default-src 'none'; style-src 'unsafe-inline'; sandbox\"", $content);
        $this->assertStringContainsString('X-Content-Type-Options "nosniff"', $content);
    }
}
