<?php

namespace Plugins\Posts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PostAuthor extends Model
{
    protected $table = 'post_authors';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'avatar',
        'bio',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($author) {
            if (empty($author->slug)) {
                $author->slug = Str::slug($author->name);

                // Ensure slug uniqueness
                $originalSlug = $author->slug;
                $count = 1;
                while (static::where('slug', $author->slug)->exists()) {
                    $author->slug = $originalSlug.'-'.$count++;
                }
            }
        });
    }

    /**
     * Get all posts by this author.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }
}
