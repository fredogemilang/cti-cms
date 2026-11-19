<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('admin breadcrumb component dynamically resolves menu items from AdminMenuBuilder', function () {
    $role = Role::create([
        'name' => 'Super Admin',
        'slug' => 'super-admin',
        'is_super_admin' => true,
    ]);

    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $response = $this->get(route('admin.pages.index'));
    $response->assertOk();
    $response->assertSee('Pages');
});
