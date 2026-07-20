<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    private const DISPLAY_CURRENCY_PRIORITY = [
        'USD' => 0,
        'CUP' => 1,
    ];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sales_count',
        'view_count',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'sales_count' => 'integer',
        'view_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class)->orderBy('currency_code');
    }

    public function skus()
    {
        return $this->hasMany(ProductSku::class)->orderBy('sort_order');
    }

    public function activeSkus()
    {
        return $this->hasMany(ProductSku::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function primaryImage()
    {
        return $this->images->first()->image_url ?? $this->image_url;
    }

    public function priceFor(string $currencyCode = 'USD')
    {
        $price = $this->prices->firstWhere('currency_code', strtoupper($currencyCode));

        return $price ? $price->effectivePrice() : $this->price;
    }

    public function displayPriceRecord()
    {
        $prices = $this->relationLoaded('prices') ? $this->prices : $this->prices()->get();

        return $prices
            ->filter(function ($price) {
                return array_key_exists($price->currency_code, self::DISPLAY_CURRENCY_PRIORITY);
            })
            ->sortBy(function ($price) {
                return self::DISPLAY_CURRENCY_PRIORITY[$price->currency_code] ?? 999;
            })
            ->first();
    }

    public function displayPrice(): float
    {
        $price = $this->displayPriceRecord();

        return $price ? $price->effectivePrice() : (float) $this->price;
    }

    public function displayRegularPrice(): float
    {
        $price = $this->displayPriceRecord();

        return $price ? (float) $price->price : (float) $this->price;
    }

    public function displayDiscountPrice(): ?float
    {
        $price = $this->displayPriceRecord();

        return $price && $price->hasDiscount() ? (float) $price->discount_price : null;
    }

    public function hasDisplayDiscount(): bool
    {
        return $this->displayDiscountPrice() !== null;
    }

    public function displayCurrency(): string
    {
        $price = $this->displayPriceRecord();

        return $price ? $price->currency_code : 'USD';
    }

    public function availableStock(): int
    {
        if ($this->relationLoaded('skus') || $this->relationLoaded('activeSkus')) {
            $skus = $this->relationLoaded('activeSkus') ? $this->activeSkus : $this->skus->where('is_active', true);

            return (int) $skus->sum('stock');
        }

        return (int) $this->activeSkus()->sum('stock');
    }
}
