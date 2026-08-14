<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Role;
use App\Models\Theme;
use App\Models\User;
use App\Services\ThemeLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormStudioValidationTest extends TestCase
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
        $this->user->assignRole(Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'is_super_admin' => true,
        ]));
    }

    #[Test]
    public function studio_save_persists_corporate_email_rule_on_email_field(): void
    {
        $form = Form::create([
            'name' => 'Contact',
            'slug' => 'contact',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post("/ctrlpanel/forms/{$form->id}/studio", [
            'name' => 'Contact',
            'slug' => 'contact',
            'fields' => [
                [
                    'label' => 'Work Email',
                    'field_id' => 'work_email',
                    'type' => 'email',
                    'is_required' => '1',
                    'validation' => json_encode([
                        'rule' => 'corporate_email',
                        'rule_message' => 'Gunakan email perusahaan.',
                    ]),
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $field = $form->fields()->first();
        $this->assertNotNull($field);
        $this->assertEquals(
            ['rule' => 'corporate_email', 'rule_message' => 'Gunakan email perusahaan.'],
            $field->validation,
        );

        // The saved field actually enforces the rule end-to-end
        $this->assertEquals('Gunakan email perusahaan.', $field->validateValue('user@gmail.com'));
        $this->assertTrue($field->validateValue('user@company.com'));
    }

    #[Test]
    public function studio_save_without_rule_does_not_add_validation(): void
    {
        $form = Form::create([
            'name' => 'Contact',
            'slug' => 'contact',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post("/ctrlpanel/forms/{$form->id}/studio", [
            'name' => 'Contact',
            'slug' => 'contact',
            'fields' => [
                [
                    'label' => 'Work Email',
                    'field_id' => 'work_email',
                    'type' => 'email',
                    'is_required' => '0',
                    'validation' => '{}',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $field = $form->fields()->first();
        $this->assertNotNull($field);
        $this->assertEquals([], $field->validation);
        $this->assertTrue($field->validateValue('user@gmail.com'));
    }
}
