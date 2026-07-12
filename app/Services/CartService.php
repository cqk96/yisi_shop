<?php

namespace App\Services;

use App\Models\ProductSku;
use Illuminate\Support\Collection;

class CartService
{
    public function raw(): array
    {
        return session('cart', []);
    }

    public function put(int $skuId, int $quantity): void
    {
        $cart = $this->raw();
        $sku = ProductSku::find($skuId);

        if (! $sku) {
            unset($cart[$skuId]);
            session(['cart' => $cart]);

            return;
        }

        $cart[$skuId] = min($sku->stock, max(1, $quantity));

        session(['cart' => $cart]);
    }

    public function add(ProductSku $sku, int $quantity): void
    {
        $cart = $this->raw();
        $currentQuantity = $cart[$sku->id] ?? 0;
        $cart[$sku->id] = min($sku->stock, $currentQuantity + max(1, $quantity));

        session(['cart' => $cart]);
    }

    public function remove(int $skuId): void
    {
        $cart = $this->raw();
        unset($cart[$skuId]);

        session(['cart' => $cart]);
    }

    public function clear(): void
    {
        session()->forget('cart');
    }

    public function summary(): array
    {
        $cart = $this->raw();
        $skus = ProductSku::with(['product.prices', 'product.images', 'product.category'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = collect($cart)
            ->map(function ($quantity, $skuId) use ($skus) {
                $sku = $skus->get((int) $skuId);

                if (! $sku || ! $sku->is_active || ! $sku->product || ! $sku->product->is_active) {
                    return null;
                }

                $quantity = min((int) $quantity, $sku->stock);
                $unitPrice = (float) $sku->product->priceFor('CNY');
                $subtotal = $unitPrice * $quantity;

                return [
                    'sku' => $sku,
                    'product' => $sku->product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            })
            ->filter()
            ->values();

        return [
            'items' => $items,
            'count' => $items->sum('quantity'),
            'total' => $items->sum('subtotal'),
        ];
    }

    public function items(): Collection
    {
        return $this->summary()['items'];
    }
}
