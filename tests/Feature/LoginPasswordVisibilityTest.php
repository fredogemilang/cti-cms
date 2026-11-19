<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginPasswordVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_password_input_with_type_password(): void
    {
        $adminPath = config('admin.path', 'admin');
        $response = $this->get('/'.$adminPath.'/login');

        $response->assertStatus(200);
        $response->assertSee('type="password"', false);
        $response->assertSee('id="password"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('id="togglePasswordBtn"', false);
        $response->assertDontSee(':type="showPassword ? \'text\' : \'password\'"', false);
    }

    public function test_user_create_page_renders_password_input_with_type_password(): void
    {
        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'is_super_admin' => true,
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertSee('type="password"', false);
    }
}
