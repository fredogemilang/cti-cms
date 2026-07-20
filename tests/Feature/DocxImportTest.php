<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Plugins\Posts\Livewire\PostForm;
use Plugins\Posts\Providers\PostsServiceProvider;
use Plugins\Posts\Services\DocxParserService;
use Tests\TestCase;
use ZipArchive;

class DocxImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Manually register the namespace in Composer's autoloader for the test environment (must run after parent::setUp so base_path works)
        $loader = require base_path('vendor/autoload.php');
        $loader->addPsr4('Plugins\\Posts\\', base_path('plugins/posts/src'));

        // 2. Manually register the plugin provider since RefreshDatabase clears DB records during boot
        app()->register(PostsServiceProvider::class);

        // 3. Seed default theme
        \DB::table('themes')->updateOrInsert(
            ['slug' => 'default'],
            [
                'name' => 'Default',
                'version' => '1.0.0',
                'description' => 'A clean, modern default theme for the Web CMS.',
                'author' => 'Web CMS',
                'is_active' => true,
                'supports' => json_encode(['pages', 'posts', 'menus']),
                'installed_at' => now(),
                'activated_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Force run plugin migrations for the posts plugin
        Artisan::call('migrate', [
            '--path' => 'plugins/posts/database/migrations',
            '--force' => true,
        ]);

        $this->user = User::factory()->create();
    }

    protected function createMockDocx(string $filePath): void
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Cannot create mock DOCX file.');
        }

        // 1. Add main document content (contains 2 Heading1 elements to trigger downlevel, image, and links)
        $documentXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <w:body>
        <w:p>
            <w:pPr>
                <w:pStyle w:val="Heading1"/>
            </w:pPr>
            <w:r>
                <w:t>Imported Blog Post Title</w:t>
            </w:r>
        </w:p>
        <w:p>
            <w:r>
                <w:rPr>
                    <w:b/>
                </w:rPr>
                <w:t>This is bold text</w:t>
            </w:r>
            <w:r>
                <w:t> and regular text.</w:t>
            </w:r>
        </w:p>
        <w:p>
            <w:r>
                <w:drawing>
                    <wp:docPr id="1" name="Picture 1" descr="" title="" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"/>
                    <a:blip r:embed="rId5" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"/>
                </w:drawing>
            </w:r>
        </w:p>
        <w:p>
            <w:pPr>
                <w:pStyle w:val="Heading1"/>
            </w:pPr>
            <w:r>
                <w:t>Second Heading 1</w:t>
            </w:r>
        </w:p>
        <w:p>
            <w:hyperlink r:id="rId6">
                <w:r>
                    <w:t>Internal Link</w:t>
                </w:r>
            </w:hyperlink>
            <w:hyperlink r:id="rId7">
                <w:r>
                    <w:t>External Link</w:t>
                </w:r>
            </w:hyperlink>
        </w:p>
    </w:body>
</w:document>
XML;
        $zip->addFromString('word/document.xml', $documentXml);

        // 2. Add relationships metadata mapping image rId5 and links rId6/rId7
        $relsXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/image1.png"/>
    <Relationship Id="rId6" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="http://localhost/about-us"/>
    <Relationship Id="rId7" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="https://google.com"/>
</Relationships>
XML;
        $zip->addFromString('word/_rels/document.xml.rels', $relsXml);

        // 3. Add mock image file
        $zip->addFromString('word/media/image1.png', 'MOCK_PNG_BINARY_CONTENT');

        $zip->close();
    }

    #[Test]
    public function docx_parser_extracts_title_formatting_and_images(): void
    {
        Storage::fake('public');
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_test').'.docx';

        $this->createMockDocx($tempFile);

        $parser = new DocxParserService;
        $result = $parser->parse($tempFile);

        $this->assertSame('Imported Blog Post Title', $result['title']);

        // Assert: HTML contains bold element
        $this->assertStringContainsString('<strong>This is bold text</strong> and regular text.', $result['content']);

        // Assert: Downlevel subheaders triggered because there are multiple Heading1 elements (H1 becomes H2)
        $this->assertStringContainsString('<h2>Imported Blog Post Title</h2>', $result['content']);
        $this->assertStringContainsString('<h2>Second Heading 1</h2>', $result['content']);

        // Assert: Generates dynamic alt & title attributes if they are empty
        $this->assertStringContainsString('alt="Imported Blog Post Title - Image 1"', $result['content']);
        $this->assertStringContainsString('title="Imported Blog Post Title - Image 1"', $result['content']);

        // Assert: Cleans site URL prefix from internal link, turning it relative
        $this->assertStringContainsString('<a href="/about-us">Internal Link</a>', $result['content']);

        // Assert: External link retains full URL and gets target="_blank"
        $this->assertStringContainsString('<a href="https://google.com" target="_blank" rel="noopener noreferrer">External Link</a>', $result['content']);

        // Assert that the image was extracted and saved to storage
        $files = Storage::disk('public')->files('uploads/posts');
        $this->assertCount(1, $files);

        unlink($tempFile);
    }

    #[Test]
    public function livewire_post_form_populates_fields_on_docx_upload(): void
    {
        Storage::fake('public');

        $tempFile = tempnam(sys_get_temp_dir(), 'docx_test').'.docx';
        $this->createMockDocx($tempFile);

        $this->actingAs($this->user);

        // Use UploadedFile::fake()->createWithContent to mock upload correctly with standard Livewire attributes
        $uploadedFile = UploadedFile::fake()->createWithContent('test.docx', file_get_contents($tempFile));

        $test = Livewire::test(PostForm::class)
            ->upload('docxFile', [$uploadedFile]);

        $this->assertSame('Imported Blog Post Title', $test->get('title'));
        $this->assertSame('imported-blog-post-title', $test->get('slug'));

        // Assert that the body parses downleveled subheaders & relative links
        $content = $test->get('content');
        $this->assertStringContainsString('<h2>Imported Blog Post Title</h2>', $content);
        $this->assertStringContainsString('<h2>Second Heading 1</h2>', $content);
        $this->assertStringContainsString('<a href="/about-us">Internal Link</a>', $content);
        $this->assertStringContainsString('alt="Imported Blog Post Title - Image 1"', $content);

        unlink($tempFile);
    }
}
