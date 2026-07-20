<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use App\Services\ThemeLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure database table for themes is seeded with default
        Theme::updateOrCreate(
            ['slug' => 'default'],
            [
                'name' => 'Default',
                'version' => '1.0.0',
                'description' => 'A clean default theme',
                'is_active' => true,
                'supports' => ['pages', 'posts', 'menus'],
            ]
        );

        // Boot ThemeLoader so activeTheme is resolved correctly during testing
        app(ThemeLoader::class)->boot();

        $this->user = User::factory()->create();
    }

    #[Test]
    public function guest_cannot_access_form_assignments(): void
    {
        $response = $this->get('/ctrlpanel/forms/assignments');
        $response->assertRedirect('/ctrlpanel/login');
    }

    #[Test]
    public function authorized_user_can_access_form_assignments_and_save(): void
    {
        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'is_super_admin' => true,
        ]);
        $this->user->roles()->attach($role->id);

        $response = $this->actingAs($this->user)->get('/ctrlpanel/forms/assignments');
        $response->assertStatus(200);
        $response->assertSee('Theme Form Assignments');
        $response->assertSee('Contact Form'); // Default theme placeholder

        // Create a dummy form
        $form = Form::create([
            'name' => 'General Inquiry Form',
            'slug' => 'general-inquiry-form',
            'is_active' => true,
        ]);

        // Submit form assignment
        $response = $this->actingAs($this->user)->post('/ctrlpanel/forms/assignments', [
            'assignments' => [
                'contact_form' => $form->id,
            ],
        ]);

        $response->assertRedirect('/ctrlpanel/forms/assignments');
        $response->assertSessionHas('success', 'Form assignments saved successfully.');

        // Assert it is saved in settings
        $saved = Setting::get('theme_default_form_assignments');
        $this->assertEquals(['contact_form' => $form->id], $saved);
    }

    #[Test]
    public function render_theme_form_helper_and_blade_directive_renders_assigned_form(): void
    {
        // 1. Unassigned placeholder should return empty string
        $this->assertEquals('', render_theme_form('contact_form'));

        // 2. Create form and assign
        $form = Form::create([
            'name' => 'General Inquiry Form',
            'slug' => 'general-inquiry-form',
            'is_active' => true,
        ]);

        // Add a field
        $form->fields()->create([
            'label' => 'Your Name',
            'field_id' => 'your_name',
            'type' => 'text',
            'order' => 0,
            'is_required' => true,
        ]);

        Setting::set('theme_default_form_assignments', ['contact_form' => $form->id], 'theme', 'array');

        // Render via helper
        $rendered = render_theme_form('contact_form');
        $this->assertStringContainsString('action="'.route('forms.submit', 'general-inquiry-form').'"', $rendered);
        $this->assertStringContainsString('name="your_name"', $rendered);
        $this->assertStringContainsString('Your Name', $rendered);

        // Render via Blade compiler
        $blade = "@form('contact_form')";
        $compiled = Blade::render($blade);
        $this->assertStringContainsString('action="'.route('forms.submit', 'general-inquiry-form').'"', $compiled);
        $this->assertStringContainsString('name="your_name"', $compiled);
    }
}
