<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasRoles;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'bio',
        'avatar',
        'password',
        'is_active',
        'last_login_at',
        'password_changed_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'failed_login_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'locked_until' => 'datetime',
            'failed_login_attempts' => 'integer',
        ];
    }

    /**
     * Get pages authored by this user.
     */
    public function pages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Page::class, 'author_id');
    }

    /**
     * Get CPT entries authored by this user.
     */
    public function cptEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CptEntry::class, 'author_id');
    }

    /**
     * Get media uploaded by this user.
     */
    public function media(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    /**
     * Get page revisions made by this user.
     */
    public function pageRevisions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PageRevision::class, 'user_id');
    }

    /**
     * Get counts of all content authored/uploaded by this user.
     *
     * @return array<string, int>
     */
    public function authoredContentCounts(): array
    {
        return [
            'pages' => Page::where('author_id', $this->id)->count(),
            'cpt_entries' => CptEntry::where('author_id', $this->id)->count(),
            'media' => Media::where('uploaded_by', $this->id)->count(),
        ];
    }

    /**
     * Check if this user owns any published/draft content.
     */
    public function hasAuthoredContent(): bool
    {
        return array_sum($this->authoredContentCounts()) > 0;
    }

    /**
     * Reassign all content authored or uploaded by this user to another target user.
     */
    public function reassignContentTo(User $targetUser): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($targetUser) {
            Page::where('author_id', $this->id)->update(['author_id' => $targetUser->id]);
            CptEntry::where('author_id', $this->id)->update(['author_id' => $targetUser->id]);
            Media::where('uploaded_by', $this->id)->update(['uploaded_by' => $targetUser->id]);
            PageRevision::where('user_id', $this->id)->update(['user_id' => $targetUser->id]);

            if (class_exists(EditorialNote::class)) {
                EditorialNote::where('user_id', $this->id)->update(['user_id' => $targetUser->id]);
            }
        });
    }
}
