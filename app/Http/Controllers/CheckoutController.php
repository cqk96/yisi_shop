<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(CartService $cart)
    {
        $summary = $cart->summary();

        if ($summary['items']->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(__('ui.messages.cart_empty_checkout'));
        }

        return view('checkout.create', [
            'cartSummary' => $summary,
        ]);
    }

    public function store(Request $request, CartService $cart)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:50'],
            'customer_email' => ['required', 'email', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:500'],
        ]);

        $summary = $cart->summary();

        if ($summary['items']->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(__('ui.messages.cart_empty_checkout'));
        }

        $order = DB::transaction(function () use ($data, $summary) {
            $order = Order::create([
                'order_no' => now()->format('YmdHis') . Str::upper(Str::random(6)),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'status' => 'pending',
                'currency' => $summary['currency'],
                'total' => $summary['total'],
            ]);

            foreach ($summary['items'] as $item) {
                $sku = $item['sku']->fresh(['product']);

                if (! $sku || $sku->stock < $item['quantity']) {
                    throw new \RuntimeException(__('ui.messages.sku_stock_not_enough'));
                }

                $product = $sku->product;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_sku_id' => $sku->id,
                    'product_name' => $product->name,
                    'sku_name' => $sku->name,
                    'sku_code' => $sku->code,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                $sku->decrement('stock', $item['quantity']);
                $product->update(['stock' => $product->activeSkus()->sum('stock')]);
            }

            return $order;
        });

        $cart->clear();

        return redirect()->route('orders.show', $order)->with('status', __('ui.messages.order_submitted'));
    }

    public function show(Order $order)
    {
        return view('checkout.show', [
            'order' => $order->load('items'),
            'cartSummary' => [
                'items' => collect(),
                'count' => 0,
                'currency' => 'USD',
                'total' => 0,
            ],
        ]);
    }
}
