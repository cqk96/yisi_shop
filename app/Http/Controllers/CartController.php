<?php

namespace App\Http\Controllers;

use App\Models\ProductSku;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cart)
    {
        return view('cart.index', [
            'cartSummary' => $cart->summary(),
        ]);
    }

    public function store(Request $request, CartService $cart)
    {
        $data = $request->validate([
            'sku_id' => ['required', 'exists:product_skus,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $sku = ProductSku::with('product')
            ->where('is_active', true)
            ->findOrFail($data['sku_id']);

        if (! $sku->product || ! $sku->product->is_active) {
            abort(404);
        }

        if ($sku->stock <= 0) {
            return back()->withErrors('该 SKU 暂时缺货。');
        }

        $cart->add($sku, (int) $data['quantity']);

        return redirect()->route('cart.index')->with('status', '商品已加入购物车。');
    }

    public function update(Request $request, ProductSku $sku, CartService $cart)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . max(1, $sku->stock)],
        ]);

        $cart->put($sku->id, (int) $data['quantity']);

        return redirect()->route('cart.index')->with('status', '购物车已更新。');
    }

    public function destroy(ProductSku $sku, CartService $cart)
    {
        $cart->remove($sku->id);

        return redirect()->route('cart.index')->with('status', '商品已移出购物车。');
    }
}
