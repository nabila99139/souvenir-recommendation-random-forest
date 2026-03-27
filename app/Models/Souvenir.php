<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Souvenir extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'price_range',
        'price',
        'description',
        'image_path',
        'image',
        'views',
        'seller_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_range' => 'string',
        'price' => 'decimal:2',
        'views' => 'integer',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'views' => 0,
    ];

    /**
     * Get the seller (user) that owns the souvenir.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Scope to query souvenirs by seller.
     */
    public function scopeBySeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Check if souvenir belongs to a specific seller.
     */
    public function belongsToSeller(int $sellerId): bool
    {
        return $this->seller_id === $sellerId;
    }

    /**
     * Increment the view count for the souvenir.
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Get the display price (use price if available, otherwise price range).
     */
    public function getDisplayPriceAttribute(): string
    {
        if ($this->price !== null) {
            return 'Rp ' . number_format($this->price, 0, ',', '.');
        }

        return match($this->price_range) {
            'low' => 'Rp 0 - 100.000',
            'medium' => 'Rp 100.000 - 500.000',
            'high' => 'Rp 500.000+',
            default => 'Price not available',
        };
    }
}
