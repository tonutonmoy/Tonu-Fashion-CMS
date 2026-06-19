<?php

namespace App\Models;

use App\Concerns\HasTranslations;
use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends BaseModel
{
    use HasFactory, HasTranslations;

    protected static function booted(): void
    {
        static::saving(function (self $product) {
            $product->effective_price = $product->sale_price ?? $product->regular_price;
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'stock',
        'featured',
        'free_delivery',
        'effective_price',
        'category_id',
        'brand_id',
        'status',
        'meta_title',
        'meta_description',
        'og_image',
        'avg_rating',
        'review_count',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'float',
            'sale_price' => 'float',
            'effective_price' => 'float',
            'featured' => 'boolean',
            'free_delivery' => 'boolean',
            'status' => RecordStatus::class,
            'avg_rating' => 'decimal:2',
            'review_count' => 'integer',
            'translations' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->regular_price);
    }

    public function getPrimaryImageAttribute(): ?string
    {
        $image = $this->primaryImageRecord();

        return $image?->path;
    }

    public function getPrimaryImageVariantsAttribute(): ?array
    {
        return $this->primaryImageRecord()?->variants;
    }

    private function primaryImageRecord(): ?ProductImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        }

        return $this->images()->orderByDesc('is_primary')->orderBy('sort_order')->first();
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->regular_price;
    }

    public function inStock(): bool
    {
        if ($this->variants->isNotEmpty()) {
            return $this->variants->where('status', RecordStatus::Active)->sum('stock') > 0;
        }

        return $this->stock > 0;
    }
}
