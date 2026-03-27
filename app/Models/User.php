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
     * User role constants
     */
    public const ROLE_ROOT = 'root';
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_SELLER = 'seller';
    public const ROLE_ADMIN = 'admin'; // Legacy support

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
        'authorized_by',
        // Business profile fields for sellers
        'business_name',
        'business_description',
        'business_address',
        'business_phone',
        'business_hours',
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
     * Get all available roles.
     */
    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_CUSTOMER => 'Customer',
            self::ROLE_SELLER => 'Seller',
            // Root role is only available via admin registration
        ];
    }

    /**
     * Get role display name.
     */
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            self::ROLE_ROOT => 'Root Admin',
            self::ROLE_CUSTOMER => 'Customer',
            self::ROLE_SELLER => 'Seller',
            self::ROLE_ADMIN => 'Admin', // Legacy
            default => 'User',
        };
    }

    /**
     * Check if user is root admin.
     */
    public function isRoot(): bool
    {
        return $this->role === self::ROLE_ROOT;
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Check if user is seller.
     */
    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    /**
     * Check if user is admin (legacy support for root).
     */
    public function isAdmin(): bool
    {
        return $this->isRoot() || $this->is_admin === true;
    }

    /**
     * Check if user is regular user (not root).
     */
    public function isRegularUser(): bool
    {
        return !$this->isAdmin();
    }

    /**
     * Check if user is root admin (no company or site association).
     * Legacy method for backward compatibility.
     */
    public function isRootAdmin(): bool
    {
        return $this->isRoot();
    }


    /**
     * Get dashboard route for the user based on role.
     */
    public function getDashboardRoute(): string
    {
        return match($this->role) {
            self::ROLE_ROOT => 'admin.dashboard',
            self::ROLE_CUSTOMER => 'home',
            self::ROLE_SELLER => 'seller.dashboard',
            default => 'home',
        };
    }

    /**
     * Make user root.
     */
    public function makeRoot(): void
    {
        $this->update([
            'role' => self::ROLE_ROOT,
            'is_admin' => true,
        ]);
    }

    /**
     * Make user customer.
     */
    public function makeCustomer(): void
    {
        $this->update([
            'role' => self::ROLE_CUSTOMER,
            'is_admin' => false,
        ]);
    }

    /**
     * Make user seller.
     */
    public function makeSeller(): void
    {
        $this->update([
            'role' => self::ROLE_SELLER,
            'is_admin' => false,
        ]);
    }

    /**
     * Make user an admin (legacy).
     */
    public function makeAdmin(): void
    {
        $this->update([
            'role' => self::ROLE_ROOT,
            'is_admin' => true,
        ]);
    }

    /**
     * Make user a regular user (legacy).
     */
    public function makeUser(): void
    {
        $this->update([
            'role' => self::ROLE_CUSTOMER,
            'is_admin' => false,
        ]);
    }

    /**
     * Scope to query only root users.
     */
    public function scopeRoot($query)
    {
        return $query->where('role', self::ROLE_ROOT);
    }

    /**
     * Scope to query only customers.
     */
    public function scopeCustomer($query)
    {
        return $query->where('role', self::ROLE_CUSTOMER);
    }

    /**
     * Scope to query only sellers.
     */
    public function scopeSeller($query)
    {
        return $query->where('role', self::ROLE_SELLER);
    }

    /**
     * Scope to query only admins (root + legacy admin).
     */
    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Get the souvenirs owned by the seller.
     */
    public function souvenirs()
    {
        return $this->hasMany(Souvenir::class, 'seller_id');
    }

    /**
     * Get the count of souvenirs owned by the seller.
     */
    public function getSouvenirCountAttribute(): int
    {
        return $this->souvenirs()->count();
    }

    /**
     * Check if user has completed business profile setup.
     */
    public function hasBusinessProfile(): bool
    {
        return $this->isSeller() &&
               !empty($this->business_name) &&
               !empty($this->business_address) &&
               !empty($this->business_phone);
    }

    /**
     * Get business display name (fallback to regular name).
     */
    public function getBusinessDisplayName(): string
    {
        return $this->business_name ?? $this->name;
    }
}
