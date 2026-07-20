<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\CustomerOrderSession;
use Illuminate\Support\Facades\DB;

class MyOrderController extends Controller
{
    public function index(CartService $cart, CustomerOrderSession $customerOrders)
    {
        $orders = Order::with('items')
            ->whereIn('id', $customerOrders->ids() ?: [0])
            ->latest()
            ->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
            'cartSummary' => $cart->summary(),
        ]);
    }

    public function show(Order $order, CartService $cart, CustomerOrderSession $customerOrders)
    {
        abort_unless($customerOrders->contains($order), 404);

        return view('checkout.show', [
            'order' => $order->load('items'),
            'cartSummary' => $cart->summary(),
        ]);
    }

    public function cancel(Order $order, CustomerOrderSession $customerOrders)
    {
        abort_unless($customerOrders->contains($order), 404);
        abort_unless(in_array($order->status, ['pending', 'paid'], true), 403);

        DB::transaction(function () use ($order) {
            $order->load('items.sku.product');

            foreach ($order->items as $item) {
                if (! $item->sku) {
                    continue;
                }

                $item->sku->increment('stock', $item->quantity);

                if ($item->sku->product) {
                    $item->sku->product->update([
                        'stock' => $item->sku->product->activeSkus()->sum('stock'),
                    ]);
                }
            }

            $order->update(['status' => 'cancelled']);
        });

        return redirect()->route('orders.show', $order)->with('status', __('ui.messages.order_cancelled'));
    }
}
