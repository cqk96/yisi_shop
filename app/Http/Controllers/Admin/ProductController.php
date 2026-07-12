<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    private array $currencies = ['CNY', 'USD', 'EUR', 'HKD'];

    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::with(['category', 'prices', 'images', 'skus'])->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true]),
            'categories' => Category::orderBy('name')->get(),
            'currencies' => $this->currencies,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $product = Product::create($data['product']);

        $this->syncImages($product, $data['images']);
        $this->syncPrices($product, $data['prices']);
        $this->syncSkus($product, $data['skus']);

        return redirect()->route('admin.products.index')->with('status', '商品已创建。');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product->load(['images', 'prices', 'skus']),
            'categories' => Category::orderBy('name')->get(),
            'currencies' => $this->currencies,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validatedData($request, $product);
        $product->update($data['product']);

        $this->syncImages($product, $data['images']);
        $this->syncPrices($product, $data['prices']);
        $this->syncSkus($product, $data['skus']);

        return redirect()->route('admin.products.index')->with('status', '商品已更新。');
    }

    public function destroy(Product $product)
    {
        $this->deleteStoredImages($product->images()->pluck('image_url')->all());
        $this->deleteStoredImage($product->image_url);

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', '商品已删除。');
    }

    private function validatedData(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150', Rule::unique('products')->ignore($product)],
            'description' => ['required', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*' => ['nullable', 'string', 'max:1000'],
            'image_files' => ['nullable', 'array'],
            'image_files.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'prices' => ['required', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'skus' => ['required', 'array'],
            'skus.*.name' => ['nullable', 'string', 'max:120'],
            'skus.*.code' => ['nullable', 'string', 'max:120'],
            'skus.*.stock' => ['nullable', 'integer', 'min:0'],
            'skus.*.is_active' => ['nullable', 'boolean'],
        ]);

        $prices = collect($data['prices'])
            ->mapWithKeys(function ($price, $currency) {
                return [strtoupper($currency) => $price];
            })
            ->filter(function ($price) {
                return $price !== null && $price !== '';
            })
            ->all();

        if (! isset($prices['CNY'])) {
            throw ValidationException::withMessages([
                'prices.CNY' => '至少需要填写 CNY 人民币价格。',
            ]);
        }

        $allowedExistingImages = $product
            ? $product->images()->pluck('image_url')->all()
            : [];

        $images = collect(Arr::get($data, 'existing_images', []))
            ->filter()
            ->filter(function ($imageUrl) use ($allowedExistingImages) {
                return in_array($imageUrl, $allowedExistingImages, true);
            })
            ->values()
            ->all();

        $images = array_merge($images, $this->storeUploadedImages($request));

        $skus = collect(Arr::get($data, 'skus', []))
            ->map(function ($sku) {
                return [
                    'name' => trim($sku['name'] ?? ''),
                    'code' => trim($sku['code'] ?? '') ?: null,
                    'stock' => (int) ($sku['stock'] ?? 0),
                    'is_active' => ! empty($sku['is_active']),
                ];
            })
            ->filter(function ($sku) {
                return $sku['name'] !== '';
            })
            ->values()
            ->all();

        if (empty($skus)) {
            throw ValidationException::withMessages([
                'skus' => '至少需要填写一个 SKU。',
            ]);
        }

        $activeStock = collect($skus)
            ->filter(function ($sku) {
                return $sku['is_active'];
            })
            ->sum('stock');

        return [
            'product' => [
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $data['slug'] ?: Str::slug($data['name']),
                'description' => $data['description'],
                'price' => $prices['CNY'],
                'stock' => $activeStock,
                'image_url' => $images[0] ?? null,
                'is_active' => $request->boolean('is_active'),
            ],
            'images' => $images,
            'prices' => $prices,
            'skus' => $skus,
        ];
    }

    private function syncImages(Product $product, array $images): void
    {
        $oldImages = $product->images()->pluck('image_url')->all();
        $removedImages = array_diff($oldImages, $images);

        $this->deleteStoredImages($removedImages);

        $product->images()->delete();

        foreach ($images as $index => $imageUrl) {
            $product->images()->create([
                'image_url' => $imageUrl,
                'alt_text' => $product->name,
                'sort_order' => $index,
            ]);
        }
    }

    private function storeUploadedImages(Request $request): array
    {
        $storedImages = [];

        foreach ($request->file('image_files', []) as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('product-images', 'public');
            $storedImages[] = Storage::disk('public')->url($path);
        }

        return $storedImages;
    }

    private function deleteStoredImages(array $imageUrls): void
    {
        foreach (array_unique(array_filter($imageUrls)) as $imageUrl) {
            $this->deleteStoredImage($imageUrl);
        }
    }

    private function deleteStoredImage(?string $imageUrl): void
    {
        if (! $imageUrl) {
            return;
        }

        $path = parse_url($imageUrl, PHP_URL_PATH) ?: $imageUrl;
        $prefix = '/storage/';

        if (! Str::startsWith($path, $prefix)) {
            return;
        }

        $storagePath = Str::after($path, $prefix);

        if (! Str::startsWith($storagePath, 'product-images/')) {
            return;
        }

        Storage::disk('public')->delete($storagePath);
    }

    private function syncPrices(Product $product, array $prices): void
    {
        $product->prices()->delete();

        foreach ($prices as $currency => $price) {
            $product->prices()->create([
                'currency_code' => $currency,
                'price' => $price,
            ]);
        }
    }

    private function syncSkus(Product $product, array $skus): void
    {
        $product->skus()->delete();

        foreach ($skus as $index => $sku) {
            $product->skus()->create([
                'name' => $sku['name'],
                'code' => $sku['code'],
                'stock' => $sku['stock'],
                'sort_order' => $index,
                'is_active' => $sku['is_active'],
            ]);
        }

        $product->update([
            'stock' => $product->activeSkus()->sum('stock'),
        ]);
    }
}
