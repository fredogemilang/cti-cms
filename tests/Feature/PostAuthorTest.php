<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
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

        // Run migrations
        Artisan::call('migrate', [
            '--path' => 'plugins/posts/database/migrations',
            '--force' => true,
        ]);

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
}
