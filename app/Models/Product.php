<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
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

    public function priceFor(string $currencyCode = 'CNY')
    {
        $price = $this->prices->firstWhere('currency_code', strtoupper($currencyCode));

        return $price ? $price->price : $this->price;
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
