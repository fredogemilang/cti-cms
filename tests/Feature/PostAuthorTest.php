<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\ThemeLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Plugins\Posts\Livewire\AuthorsManager;
use Plugins\Posts\Livewire\PostForm;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\PostAuthor;
use Plugins\Posts\Providers\PostsServiceProvider;
use Tests\TestCase;

class PostAuthorTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Register plugin autoloader
        $loader = require base_path('vendor/autoload.php');
        $loader->addPsr4('Plugins\\Posts\\', base_path('plugins/posts/src'));

        // Register plugin provider
        app()->register(PostsServiceProvider::class);

        // Seed default theme
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

        // Boot ThemeLoader so view paths are registered
        app(ThemeLoader::class)->boot();

        // Run migrations
        Artisan::call('migrate', [
            '--path' => 'plugins/posts/database/migrations',
            '--force' => true,
        ]);

        // Refresh route name lookups
        app('router')->getRoutes()->refreshNameLookups();

        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_create_standalone_post_author(): void
    {
        $author = PostAuthor::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'bio' => 'A great writer.',
        ]);

        $this->assertDatabaseHas('post_authors', [
            'id' => $author->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'slug' => 'john-doe',
        ]);
    }

    #[Test]
    public function post_belongs_to_post_author_not_user(): void
    {
        $author = PostAuthor::create([
            'name' => 'Jane Smith',
        ]);

        $post = Post::create([
            'title' => 'Sample Post',
            'slug' => 'sample-post',
            'author_id' => $author->id,
            'status' => 'draft',
        ]);

        $this->assertInstanceOf(PostAuthor::class, $post->author);
        $this->assertEquals($author->id, $post->author->id);
        $this->assertEquals('Jane Smith', $post->author->name);
    }

    #[Test]
    public function it_can_create_author_inline_in_livewire_component(): void
    {
        $this->actingAs($this->user);

        $test = Livewire::test(PostForm::class);
        $initialAuthorsCount = PostAuthor::count();

        $test->call('addAuthor', 'New Inline Author');

        // Assert author is created and selected
        $this->assertDatabaseHas('post_authors', [
            'name' => 'New Inline Author',
            'slug' => 'new-inline-author',
        ]);

        $newAuthor = PostAuthor::where('name', 'New Inline Author')->first();
        $this->assertNotNull($newAuthor);
        $this->assertEquals($newAuthor->id, $test->get('author_id'));
        $this->assertEquals($initialAuthorsCount + 1, PostAuthor::count());
    }

    #[Test]
    public function authorized_user_can_access_authors_page(): void
    {
        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'is_super_admin' => true,
        ]);
        $this->user->roles()->attach($role->id);

        $response = $this->actingAs($this->user)->get('/ctrlpanel/posts/authors');
        $response->assertStatus(200);
        $response->assertSeeLivewire('plugins.authors-manager');
    }

    #[Test]
    public function authors_manager_handles_crud_operations(): void
    {
        $this->actingAs($this->user);

        // 1. Store
        $test = Livewire::test(AuthorsManager::class)
            ->set('name', 'John CRUD')
            ->set('slug', 'john-crud')
            ->set('email', 'crud@example.com')
            ->set('bio', 'CRUD developer bio')
            ->call('store');

        $this->assertDatabaseHas('post_authors', [
            'name' => 'John CRUD',
            'slug' => 'john-crud',
            'email' => 'crud@example.com',
            'bio' => 'CRUD developer bio',
        ]);

        $author = PostAuthor::where('slug', 'john-crud')->first();

        // 2. Edit & Update
        $test->call('edit', $author->id)
            ->set('name', 'John CRUD Edited')
            ->call('update');

        $this->assertDatabaseHas('post_authors', [
            'id' => $author->id,
            'name' => 'John CRUD Edited',
        ]);

        // 3. Delete
        $test->call('delete', $author->id);
        $this->assertDatabaseMissing('post_authors', [
            'id' => $author->id,
        ]);
    }

    #[Test]
    public function visiting_a_single_post_increments_its_views_count(): void
    {
        $author = PostAuthor::create([
            'name' => 'Writer',
            'slug' => 'writer',
        ]);

        $post = Post::create([
            'title' => 'Sample View Test Post',
            'slug' => 'sample-view-test-post',
            'content' => 'Sample content',
            'status' => 'published',
            'author_id' => $author->id,
            'views_count' => 0,
        ]);

        $this->assertEquals(0, $post->fresh()->views_count);

        // Visit the single post page
        $response = $this->get('/blog/sample-view-test-post');
        $response->assertStatus(200);

        // Assert views_count incremented to 1
        $this->assertEquals(1, $post->fresh()->views_count);

        // Visit it again
        $this->get('/blog/sample-view-test-post');

        // Assert views_count incremented to 2
        $this->assertEquals(2, $post->fresh()->views_count);
    }
}
