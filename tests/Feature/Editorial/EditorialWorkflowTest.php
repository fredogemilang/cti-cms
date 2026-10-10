<?php

namespace Tests\Feature\Editorial;

use App\Livewire\Admin\Cpt\Entries\EntryForm;
use App\Livewire\Admin\Pages\PageForm;
use App\Mcp\Tools\Content\CreatePageTool;
use App\Mcp\Tools\Content\UpdatePageTool;
use App\Models\ApiToken;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AdminAlert;
use App\Services\EditorialWorkflowService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Laravel\Mcp\Request as McpRequest;
use Livewire\Livewire;
use Plugins\Posts\Livewire\PostForm;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\PostAuthor;
use Plugins\Posts\Providers\PostsServiceProvider;
use Tests\TestCase;

class EditorialWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;

    protected User $approver;

    protected Role $authorRole;

    protected Role $approverRole;

    protected Permission $approvePermission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(PostsServiceProvider::class);

        // Run posts plugin migrations for post test
        $this->artisan('migrate', [
            '--path' => 'plugins/posts/database/migrations',
            '--realpath' => false,
        ]);

        if (! Schema::hasColumn('posts', 'translations')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('translations')->nullable();
            });
        }

        $this->approvePermission = Permission::firstOrCreate(
            ['name' => 'content.approve'],
            [
                'name' => 'content.approve',
                'module' => 'editorial',
                'action' => 'approve',
                'description' => 'Approve and publish editorial content',
                'source' => 'core',
            ]
        );

        $this->authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
            'is_super_admin' => false,
        ]);

        $this->approverRole = Role::create([
            'name' => 'Editor Approver',
            'slug' => 'approver',
            'is_super_admin' => false,
        ]);
        $this->approverRole->permissions()->attach($this->approvePermission->id);

        $this->author = User::factory()->create();
        $this->author->roles()->attach($this->authorRole->id);

        $this->approver = User::factory()->create();
        $this->approver->roles()->attach($this->approverRole->id);
    }

    public function test_editorial_workflow_service_distinguishes_approvers(): void
    {
        $service = app(EditorialWorkflowService::class);

        $this->assertFalse($service->canApprove($this->author));
        $this->assertTrue($service->canApprove($this->approver));

        // Non-approver allowed statuses
        $authorStatuses = $service->allowedStatuses($this->author);
        $this->assertArrayHasKey('draft', $authorStatuses);
        $this->assertArrayHasKey('pending_review', $authorStatuses);
        $this->assertArrayNotHasKey('published', $authorStatuses);
        $this->assertArrayNotHasKey('scheduled', $authorStatuses);

        // Approver allowed statuses
        $approverStatuses = $service->allowedStatuses($this->approver);
        $this->assertArrayHasKey('published', $approverStatuses);
        $this->assertArrayHasKey('scheduled', $approverStatuses);
    }

    public function test_page_form_downgrades_published_to_pending_review_for_non_approver(): void
    {
        Notification::fake();

        $this->actingAs($this->author);

        Livewire::test(PageForm::class)
            ->set('title', 'Author Proposed Strategy')
            ->set('slug', 'author-proposed-strategy')
            ->set('status', 'published')
            ->call('save')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'author-proposed-strategy')->first();
        $this->assertNotNull($page);
        $this->assertEquals('pending_review', $page->status);

        Notification::assertSentTo(
            $this->approver,
            AdminAlert::class,
            fn ($notification) => $notification->title === 'Content Pending Review'
        );
    }

    public function test_page_form_downgrades_scheduled_to_pending_review_for_non_approver(): void
    {
        $this->actingAs($this->author);

        Livewire::test(PageForm::class)
            ->set('title', 'Author Scheduled Announcement')
            ->set('slug', 'author-scheduled-announcement')
            ->set('status', 'scheduled')
            ->set('publishedAt', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'author-scheduled-announcement')->first();
        $this->assertNotNull($page);
        $this->assertEquals('pending_review', $page->status);
    }

    public function test_page_form_allows_published_for_approver(): void
    {
        $this->actingAs($this->approver);

        Livewire::test(PageForm::class)
            ->set('title', 'Executive Published Directive')
            ->set('slug', 'executive-published-directive')
            ->set('status', 'published')
            ->call('save')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'executive-published-directive')->first();
        $this->assertNotNull($page);
        $this->assertEquals('published', $page->status);
    }

    public function test_approver_can_approve_and_publish_pending_page(): void
    {
        Notification::fake();

        $page = Page::create([
            'title' => 'Pending Feature Article',
            'slug' => 'pending-feature-article',
            'status' => 'pending_review',
            'author_id' => $this->author->id,
        ]);

        $this->actingAs($this->approver);

        Livewire::test(PageForm::class, ['id' => $page->id])
            ->call('approveAndPublish')
            ->assertDispatched('notify');

        $this->assertEquals('published', $page->fresh()->status);

        Notification::assertSentTo(
            $this->author,
            AdminAlert::class,
            fn ($notification) => $notification->title === 'Content Approved & Published'
        );
    }

    public function test_approver_can_request_changes_with_editorial_note(): void
    {
        Notification::fake();

        $page = Page::create([
            'title' => 'Draft Under Critique',
            'slug' => 'draft-under-critique',
            'status' => 'pending_review',
            'author_id' => $this->author->id,
        ]);

        $this->actingAs($this->approver);

        Livewire::test(PageForm::class, ['id' => $page->id])
            ->set('changeRequestNote', 'Please cite the enterprise benchmarks in section 3.')
            ->call('submitChangeRequest')
            ->assertDispatched('notify');

        $this->assertEquals('draft', $page->fresh()->status);

        $this->assertDatabaseHas('editorial_notes', [
            'noteable_type' => Page::class,
            'noteable_id' => $page->id,
            'user_id' => $this->approver->id,
            'note' => 'Please cite the enterprise benchmarks in section 3.',
        ]);

        Notification::assertSentTo(
            $this->author,
            AdminAlert::class,
            fn ($notification) => $notification->title === 'Changes Requested'
        );
    }

    public function test_cpt_entry_form_downgrades_to_pending_review_for_non_approver(): void
    {
        $cpt = CustomPostType::create([
            'name' => 'Solutions',
            'singular_label' => 'Solution',
            'plural_label' => 'Solutions',
            'slug' => 'solutions',
            'is_active' => true,
        ]);

        $this->actingAs($this->author);

        Livewire::test(EntryForm::class, ['postType' => $cpt])
            ->set('title', 'AI Observability Solution')
            ->set('slug', 'ai-observability-solution')
            ->set('status', 'published')
            ->call('save')
            ->assertHasNoErrors();

        $entry = CptEntry::where('slug', 'ai-observability-solution')->first();
        $this->assertNotNull($entry);
        $this->assertEquals('pending_review', $entry->status);
    }

    public function test_post_form_downgrades_to_pending_review_for_non_approver(): void
    {
        $postAuthor = PostAuthor::create([
            'name' => 'Tech Author',
            'slug' => 'tech-author',
        ]);

        $this->actingAs($this->author);

        Livewire::test(PostForm::class)
            ->set('title', 'Multi Cloud Security Deep Dive')
            ->set('slug', 'multi-cloud-security-deep-dive')
            ->set('content', '<p>In-depth exploration of zero trust.</p>')
            ->set('status', 'published')
            ->set('author_id', $postAuthor->id)
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::where('slug', 'multi-cloud-security-deep-dive')->first();
        $this->assertNotNull($post);
        $this->assertEquals('pending_review', $post->status);
    }

    public function test_mcp_create_page_tool_downgrades_when_token_user_not_approver(): void
    {
        $tokenData = ApiToken::generateFor(
            $this->author,
            'Author MCP Token',
            ['mcp.write', 'mcp.content.publish']
        );

        $httpRequest = HttpRequest::create('/api/mcp/tools/create-page', 'POST');
        $httpRequest->attributes->set('api_token', $tokenData['model']);
        $this->app->instance('request', $httpRequest);

        $mcpRequest = new McpRequest([
            'title' => 'MCP Submitted Document',
            'slug' => 'mcp-submitted-doc',
            'status' => 'published',
        ]);

        $tool = new CreatePageTool;
        $response = $tool->handle($mcpRequest);

        $page = Page::where('slug', 'mcp-submitted-doc')->first();
        $this->assertNotNull($page);
        $this->assertEquals('pending_review', $page->status);
    }

    public function test_mcp_update_page_tool_downgrades_when_token_user_not_approver(): void
    {
        $page = Page::create([
            'title' => 'Original Draft Page',
            'slug' => 'original-draft-page',
            'status' => 'draft',
            'author_id' => $this->author->id,
        ]);

        $tokenData = ApiToken::generateFor(
            $this->author,
            'Author MCP Token',
            ['mcp.write', 'mcp.content.publish']
        );

        $httpRequest = HttpRequest::create('/api/mcp/tools/update-page', 'POST');
        $httpRequest->attributes->set('api_token', $tokenData['model']);
        $this->app->instance('request', $httpRequest);

        $mcpRequest = new McpRequest([
            'id' => $page->id,
            'status' => 'published',
        ]);

        $tool = new UpdatePageTool;
        $response = $tool->handle($mcpRequest);

        $this->assertEquals('pending_review', $page->fresh()->status);
    }

    /**
     * Regression: the workflow service used to be resolved only inside the
     * published/scheduled branch, so an author explicitly submitting
     * `pending_review` over MCP never reached handleTransition() and no
     * approver was ever notified — while the UI path notified correctly.
     */
    public function test_mcp_explicit_pending_review_notifies_approvers(): void
    {
        Notification::fake();

        $page = Page::create([
            'title' => 'Draft Awaiting Submission',
            'slug' => 'draft-awaiting-submission',
            'status' => 'draft',
            'author_id' => $this->author->id,
        ]);

        $tokenData = ApiToken::generateFor(
            $this->author,
            'Author MCP Token',
            ['mcp.write']
        );

        $httpRequest = HttpRequest::create('/api/mcp/tools/update-page', 'POST');
        $httpRequest->attributes->set('api_token', $tokenData['model']);
        $this->app->instance('request', $httpRequest);

        $tool = new UpdatePageTool;
        $tool->handle(new McpRequest([
            'id' => $page->id,
            'status' => 'pending_review',
        ]));

        $this->assertEquals('pending_review', $page->fresh()->status);
        Notification::assertSentTo($this->approver, AdminAlert::class);
    }

    /**
     * `content:publish-scheduled` filters on whereNotNull('published_at'), so a
     * scheduled page without one would sit unpublished forever. The tool must
     * refuse rather than create that silent dead end.
     */
    public function test_mcp_scheduled_without_published_at_is_rejected(): void
    {
        $page = Page::create([
            'title' => 'To Be Scheduled',
            'slug' => 'to-be-scheduled',
            'status' => 'draft',
            'author_id' => $this->approver->id,
        ]);

        $tokenData = ApiToken::generateFor(
            $this->approver,
            'Approver MCP Token',
            ['mcp.write', 'mcp.content.publish']
        );

        $httpRequest = HttpRequest::create('/api/mcp/tools/update-page', 'POST');
        $httpRequest->attributes->set('api_token', $tokenData['model']);
        $this->app->instance('request', $httpRequest);

        $tool = new UpdatePageTool;
        $response = $tool->handle(new McpRequest([
            'id' => $page->id,
            'status' => 'scheduled',
        ]));

        $this->assertStringContainsString('requires "published_at"', (string) $response->content());
        $this->assertEquals('draft', $page->fresh()->status);

        // With a date supplied it goes through and stays schedulable.
        $response = $tool->handle(new McpRequest([
            'id' => $page->id,
            'status' => 'scheduled',
            'published_at' => now()->addDay()->toIso8601String(),
        ]));

        $page->refresh();
        $this->assertEquals('scheduled', $page->status);
        $this->assertNotNull($page->published_at);
    }
}
