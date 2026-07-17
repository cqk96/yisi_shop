<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index(Request $request, CartService $cart)
    {
        $categories = Category::orderBy('name')->get();
        $selectedCategory = $request->query('category');
        $keyword = $request->query('q');

        $products = Product::with(['category', 'images', 'prices', 'activeSkus'])
            ->where('is_active', true)
            ->when($selectedCategory, function ($query, $slug) {
                $query->whereHas('category', function ($categoryQuery) use ($slug) {
                    $categoryQuery->where('slug', $slug);
                });
            })
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($productQuery) use ($keyword) {
                    $productQuery
                        ->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $selectedCategory,
            'keyword' => $keyword,
            'cartSummary' => $cart->summary(),
        ]);
    }

    public function show(Product $product, CartService $cart)
    {
        abort_unless($product->is_active, 404);

        $product->increment('view_count');

        return view('shop.show', [
            'product' => $product->load(['category', 'images', 'prices', 'activeSkus']),
            'cartSummary' => $cart->summary(),
        ]);
    }
}
