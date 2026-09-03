<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\CmsServer;
use App\Mcp\Tools\Content\DeletePageTool;
use App\Mcp\Tools\Media\UploadMediaTool;
use App\Mcp\Tools\Settings\GetSettingsTool;
use App\Mcp\Tools\Settings\UpdateSettingTool;
use App\Models\ApiToken;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Transport\FakeTransporter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class McpServerTest extends TestCase
{
    use RefreshDatabase;

    protected function getResponseText(Response|ResponseFactory $response): string
    {
        if ($response instanceof ResponseFactory) {
            return (string) $response->responses()->first()->content();
        }

        return (string) $response->content();
    }

    #[Test]
    public function cms_server_registers_all_tools_resources_and_prompts(): void
    {
        $server = new CmsServer(new FakeTransporter);
        $context = $server->createContext();

        $this->assertSame('CTI CMS', $context->implementation->name);
        $this->assertCount(31, $context->tools());
        $this->assertCount(5, $context->resources());
        $this->assertCount(1, $context->resourceTemplates());
        $this->assertCount(4, $context->prompts());
    }

    #[Test]
    public function mcp_tiers_definition_is_well_formed(): void
    {
        $tiers = ApiToken::mcpTiers();

        $this->assertArrayHasKey('readonly', $tiers);
        $this->assertArrayHasKey('editor', $tiers);
        $this->assertArrayHasKey('developer', $tiers);
        $this->assertArrayHasKey('admin', $tiers);
        $this->assertArrayHasKey('chatbot', $tiers);

        foreach ($tiers as $key => $tier) {
            $this->assertNotEmpty($tier['label']);
            $this->assertNotEmpty($tier['abilities']);
            $this->assertGreaterThan(0, $tier['rate_limit']);
            $this->assertContains(ApiToken::MCP_CONNECT, $tier['abilities']);
        }
    }

    #[Test]
    public function setting_is_sensitive_key_correctly_identifies_secrets(): void
    {
        $this->assertTrue(Setting::isSensitiveKey('mail_password'));
        $this->assertTrue(Setting::isSensitiveKey('recaptcha_secret_key'));
        $this->assertTrue(Setting::isSensitiveKey('google_analytics_api_secret'));
        $this->assertTrue(Setting::isSensitiveKey('github_token'));
        $this->assertTrue(Setting::isSensitiveKey('webhook_signing_secret'));

        $this->assertFalse(Setting::isSensitiveKey('site_name'));
        $this->assertFalse(Setting::isSensitiveKey('site_description'));
        $this->assertFalse(Setting::isSensitiveKey('posts_per_page'));
    }

    #[Test]
    public function delete_page_tool_requires_two_step_confirmation_with_single_use_nonce(): void
    {
        $user = User::factory()->create();
        $generated = ApiToken::generateFor($user, '[MCP] Dev', ApiToken::mcpFullAbilities());
        request()->attributes->set('api_token', $generated['model']);

        try {
            $page = Page::create([
                'title' => 'Page To Delete',
                'slug' => 'page-to-delete',
                'status' => 'published',
                'template' => 'default',
                'author_id' => $user->id,
            ]);

            $tool = new DeletePageTool;

            // Step 1: Call without confirmation_token -> must return confirmation prompt + random nonce
            $step1Response = $tool->handle(new Request([
                'id' => $page->id,
            ]));

            $this->assertInstanceOf(ResponseFactory::class, $step1Response);
            $step1Data = json_decode($this->getResponseText($step1Response), true);

            $this->assertTrue($step1Data['requires_confirmation'] ?? false);
            $nonce = $step1Data['confirmation_token'] ?? null;
            $this->assertIsString($nonce);
            $this->assertSame(40, strlen($nonce));

            // Verify nonce was placed in Cache with 5-minute TTL
            $cacheKey = "mcp_confirm_delete_page_{$page->id}";
            $this->assertTrue(Cache::has($cacheKey));
            $this->assertSame($nonce, Cache::get($cacheKey));

            // Verify page is NOT yet deleted
            $this->assertNull($page->fresh()->deleted_at);

            // Step 2 with WRONG nonce -> must return error and NOT delete page
            $wrongResponse = $tool->handle(new Request([
                'id' => $page->id,
                'confirmation_token' => 'invalid-incorrect-nonce-value',
            ]));

            $this->assertInstanceOf(Response::class, $wrongResponse);
            $this->assertStringContainsString('Invalid or expired confirmation token', $this->getResponseText($wrongResponse));
            $this->assertNull($page->fresh()->deleted_at);
            $this->assertTrue(Cache::has($cacheKey), 'Nonce must remain in cache when wrong token is submitted');

            // Step 2 with CORRECT nonce -> must delete page and consume nonce
            $step2Response = $tool->handle(new Request([
                'id' => $page->id,
                'confirmation_token' => $nonce,
            ]));

            $this->assertInstanceOf(ResponseFactory::class, $step2Response);
            $step2Data = json_decode($this->getResponseText($step2Response), true);
            $this->assertTrue($step2Data['success'] ?? false);

            // Verify page is soft-deleted
            $this->assertNotNull($page->fresh()->deleted_at);
            $this->assertTrue($page->fresh()->trashed());

            // Verify nonce was purged from Cache (single-use guarantee)
            $this->assertFalse(Cache::has($cacheKey));

            // Replay attack test: Call again with the exact same nonce -> must be rejected
            $replayResponse = $tool->handle(new Request([
                'id' => $page->id,
                'confirmation_token' => $nonce,
            ]));

            $this->assertInstanceOf(Response::class, $replayResponse);
            // Since page is trashed, Page::find($id) will return 404/not found error
            $this->assertStringContainsString('not found', $this->getResponseText($replayResponse));
        } finally {
            request()->attributes->remove('api_token');
        }
    }

    #[Test]
    public function content_feed_endpoint_requires_auth_and_validates_locale(): void
    {
        // Unauthenticated request should be rejected
        $response = $this->getJson('/api/v1/content-feed/en');
        $response->assertStatus(401);

        // Authenticated request with ApiToken via generateFor
        $user = User::factory()->create();
        $generated = ApiToken::generateFor(
            $user,
            '[MCP] Test Chatbot',
            ApiToken::mcpReadOnlyAbilities()
        );

        $plaintext = $generated['plaintext'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$plaintext}",
        ])->getJson('/api/v1/content-feed/invalid_locale');

        $response->assertStatus(400);

        $responseValid = $this->withHeaders([
            'Authorization' => "Bearer {$plaintext}",
        ])->getJson('/api/v1/content-feed/en');

        $responseValid->assertStatus(200);
        $responseValid->assertJsonStructure([
            'site' => ['name', 'url', 'locale', 'available_locales'],
            'sync' => ['since', 'last_modified', 'generated_at'],
            'pages',
            'cpt',
            'posts',
        ]);
    }

    #[Test]
    public function update_setting_tool_blocks_sensitive_and_unregistered_settings(): void
    {
        $user = User::factory()->create();
        $generated = ApiToken::generateFor($user, '[MCP] Admin', ApiToken::mcpFullAbilities());
        request()->attributes->set('api_token', $generated['model']);

        try {
            $tool = new UpdateSettingTool;

            // Sensitive key should be blocked
            $response = $tool->handle(new Request([
                'key' => 'mail_password',
                'value' => 'new-secret',
            ]));
            $this->assertInstanceOf(Response::class, $response);
            $this->assertStringContainsString('sensitive credentials', $this->getResponseText($response));

            // Unregistered key should be blocked
            $response2 = $tool->handle(new Request([
                'key' => 'completely_random_unknown_key',
                'value' => 'val',
            ]));
            $this->assertInstanceOf(Response::class, $response2);
            $this->assertStringContainsString('does not exist', $this->getResponseText($response2));
        } finally {
            request()->attributes->remove('api_token');
        }
    }

    #[Test]
    public function get_settings_tool_filters_out_sensitive_keys(): void
    {
        $user = User::factory()->create();
        $generated = ApiToken::generateFor($user, '[MCP] Reader', ApiToken::mcpReadOnlyAbilities());
        request()->attributes->set('api_token', $generated['model']);

        try {
            Setting::set('site_name', 'My Test Site', 'general', 'string');
            Setting::set('mail_password', 'super-secret', 'mail', 'password');

            $tool = new GetSettingsTool;
            $response = $tool->handle(new Request([
                'keys' => ['site_name', 'mail_password'],
            ]));

            $text = $this->getResponseText($response);
            $data = json_decode($text, true);

            $this->assertArrayHasKey('site_name', $data['settings']);
            $this->assertArrayNotHasKey('mail_password', $data['settings']);
        } finally {
            request()->attributes->remove('api_token');
        }
    }

    #[Test]
    public function upload_media_tool_rejects_disallowed_extensions(): void
    {
        $user = User::factory()->create();
        $generated = ApiToken::generateFor($user, '[MCP] Editor', ApiToken::mcpEditorAbilities());
        request()->attributes->set('api_token', $generated['model']);

        try {
            $tool = new UploadMediaTool;
            $response = $tool->handle(new Request([
                'filename' => 'backdoor.php',
                'base64_data' => base64_encode('<?php phpinfo(); ?>'),
            ]));

            $this->assertInstanceOf(Response::class, $response);
            $this->assertStringContainsString('not allowed', $this->getResponseText($response));
        } finally {
            request()->attributes->remove('api_token');
        }
    }
}
