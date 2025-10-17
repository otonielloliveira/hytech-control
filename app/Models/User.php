<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'is_admin',
        'is_author',
        'can_create_posts',
        'can_edit_posts',
        'can_delete_posts',
        'can_publish_posts',
        'can_edit_own_posts_only',
        'can_moderate_comments',
        'can_manage_categories',
        'can_manage_tags',
        'can_manage_users',
        'can_manage_settings',
        'can_manage_store',
        'can_manage_donations',
        'can_manage_courses',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'is_admin' => 'boolean',
            'is_author' => 'boolean',
            'can_create_posts' => 'boolean',
            'can_edit_posts' => 'boolean',
            'can_delete_posts' => 'boolean',
            'can_publish_posts' => 'boolean',
            'can_edit_own_posts_only' => 'boolean',
            'can_moderate_comments' => 'boolean',
            'can_manage_categories' => 'boolean',
            'can_manage_tags' => 'boolean',
            'can_manage_users' => 'boolean',
            'can_manage_settings' => 'boolean',
            'can_manage_store' => 'boolean',
            'can_manage_donations' => 'boolean',
            'can_manage_courses' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Blog Relationships
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function publishedPosts(): HasMany
    {
        return $this->posts()->published();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Blog Methods
    public function canComment(): bool
    {
        return true; // ou sua lógica personalizada
    }

    public function isAuthor(): bool
    {
        return $this->is_author || $this->is_admin;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('images/default-avatar.png');
    }

    // Permission Methods
    public function hasPermission(string $permission): bool
    {
        // Admins sempre têm todas as permissões
        if ($this->is_admin) {
            return true;
        }

        // Verificar se o usuário está ativo
        if (!$this->is_active) {
            return false;
        }

        // Verificar permissão específica
        return $this->{$permission} ?? false;
    }

    public function canCreatePosts(): bool
    {
        return $this->hasPermission('can_create_posts');
    }

    public function canEditPosts(): bool
    {
        return $this->hasPermission('can_edit_posts');
    }

    public function canDeletePosts(): bool
    {
        return $this->hasPermission('can_delete_posts');
    }

    public function canPublishPosts(): bool
    {
        return $this->hasPermission('can_publish_posts');
    }

    public function canEditPost($post): bool
    {
        if (!$this->canEditPosts()) {
            return false;
        }

        // Se pode editar apenas seus próprios posts
        if ($this->can_edit_own_posts_only && !$this->is_admin) {
            return $post->user_id === $this->id;
        }

        return true;
    }

    public function canDeletePost($post): bool
    {
        if (!$this->canDeletePosts()) {
            return false;
        }

        // Se pode editar apenas seus próprios posts, vale para delete também
        if ($this->can_edit_own_posts_only && !$this->is_admin) {
            return $post->user_id === $this->id;
        }

        return true;
    }

    public function canModerateComments(): bool
    {
        return $this->hasPermission('can_moderate_comments');
    }

    public function canManageCategories(): bool
    {
        return $this->hasPermission('can_manage_categories');
    }

    public function canManageTags(): bool
    {
        return $this->hasPermission('can_manage_tags');
    }

    public function canManageUsers(): bool
    {
        return $this->hasPermission('can_manage_users');
    }

    public function canManageSettings(): bool
    {
        return $this->hasPermission('can_manage_settings');
    }

    public function canManageStore(): bool
    {
        return $this->hasPermission('can_manage_store');
    }

    public function canManageDonations(): bool
    {
        return $this->hasPermission('can_manage_donations');
    }

    public function canManageCourses(): bool
    {
        return $this->hasPermission('can_manage_courses');
    }

    // Métodos para definir perfis de permissão
    public function setEditorProfile(): void
    {
        $this->update([
            'is_author' => true,
            'can_create_posts' => true,
            'can_edit_posts' => true,
            'can_publish_posts' => false,
            'can_delete_posts' => false,
            'can_edit_own_posts_only' => true,
        ]);
    }

    public function setAuthorProfile(): void
    {
        $this->update([
            'is_author' => true,
            'can_create_posts' => true,
            'can_edit_posts' => true,
            'can_publish_posts' => true,
            'can_delete_posts' => true,
            'can_edit_own_posts_only' => true,
            'can_moderate_comments' => false,
        ]);
    }

    public function setModeratorProfile(): void
    {
        $this->update([
            'is_author' => true,
            'can_create_posts' => true,
            'can_edit_posts' => true,
            'can_publish_posts' => true,
            'can_delete_posts' => true,
            'can_edit_own_posts_only' => false,
            'can_moderate_comments' => true,
            'can_manage_categories' => true,
            'can_manage_tags' => true,
        ]);
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
