<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'cid',
        'sid',
        'authorized_by',
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
        ];
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->role === 'admin';
    }

    /**
     * Check if user is regular user.
     */
    public function isRegularUser(): bool
    {
        return !$this->isAdmin();
    }

    /**
     * Check if user is root admin (no company or site association).
     */
    public function isRootAdmin(): bool
    {
        return $this->isAdmin() && $this->cid === null && $this->sid === null;
    }

    /**
     * Check if user has company access.
     */
    public function hasCompanyAccess(): bool
    {
        return $this->cid !== null;
    }

    /**
     * Check if user has site access.
     */
    public function hasSiteAccess(): bool
    {
        return $this->sid !== null;
    }

    /**
     * Make user an admin.
     */
    public function makeAdmin(): void
    {
        $this->update([
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    /**
     * Make user a regular user.
     */
    public function makeUser(): void
    {
        $this->update([
            'role' => 'user',
            'is_admin' => false,
        ]);
    }
}
