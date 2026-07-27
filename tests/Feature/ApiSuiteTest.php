<?php

namespace Tests\Feature;

use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_active' => true]);

        // Create initial dummy page & CPT for route resolution
        Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->admin->id,
        ]);

        CustomPostType::create([
            'name' => 'Products',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'slug' => 'products',
            'is_active' => true,
        ]);
    }

    /**
     * Test public read-only API endpoints return HTTP 200
     */
    public function test_public_api_endpoints_return_success(): void
    {
        $this->getJson('/api/v1/pages')->assertStatus(200);
        $this->getJson('/api/v1/pages/test-page')->assertStatus(200);
        $this->getJson('/api/v1/cpt/products')->assertStatus(200);
        $this->getJson('/api/v1/menus')->assertStatus(200);
        $this->getJson('/api/v1/taxonomies')->assertStatus(200);
        $this->getJson('/api/v1/settings/public')->assertStatus(200);
        $this->getJson('/api/v1/redirects')->assertStatus(200);
        $this->getJson('/api/v1/openapi.json')->assertStatus(200);
    }

    /**
     * Test admin management API endpoints return HTTP 200 for authenticated admin
     */
    public function test_admin_api_endpoints_return_success(): void
    {
        $this->actingAs($this->admin);

        // Schema & Content Management
        $this->getJson('/api/v1/admin/cpt')->assertStatus(200);
        $this->getJson('/api/v1/admin/cpt/products/entries')->assertStatus(200);
        $this->getJson('/api/v1/admin/pages')->assertStatus(200);

        // Menus, Forms, Taxonomies
        $this->getJson('/api/v1/admin/menus')->assertStatus(200);
        $this->getJson('/api/v1/admin/forms')->assertStatus(200);
        $this->getJson('/api/v1/admin/taxonomies')->assertStatus(200);

        // System Settings, Users, Roles, Activity Logs
        $this->getJson('/api/v1/admin/settings')->assertStatus(200);
        $this->getJson('/api/v1/admin/settings/general')->assertStatus(200);
        $this->getJson('/api/v1/admin/users')->assertStatus(200);
        $this->getJson('/api/v1/admin/roles')->assertStatus(200);
        $this->getJson('/api/v1/admin/activity-logs')->assertStatus(200);

        // Plugins, Themes, Webhooks, Redirects, Email Templates, Indexing Logs
        $this->getJson('/api/v1/admin/plugins')->assertStatus(200);
        $this->getJson('/api/v1/admin/themes')->assertStatus(200);
        $this->getJson('/api/v1/admin/webhooks')->assertStatus(200);
        $this->getJson('/api/v1/admin/redirects')->assertStatus(200);
        $this->getJson('/api/v1/admin/email-templates')->assertStatus(200);
        $this->getJson('/api/v1/admin/seo/indexing-logs')->assertStatus(200);
    }
}
