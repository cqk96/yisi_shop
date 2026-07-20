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
    private array $currencies = ['USD', 'CUP'];

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
        $product->update(['slug' => (string) $product->id]);

        $this->syncImages($product, $data['images']);
        $this->syncPrices($product, $data['prices']);
        $this->syncSkus($product, $data['skus']);

        return redirect()->route('admin.products.index')->with('status', __('ui.messages.product_created'));
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

        return redirect()->route('admin.products.index')->with('status', __('ui.messages.product_updated'));
    }

    public function destroy(Product $product)
    {
        $this->deleteStoredImages($product->images()->pluck('image_url')->all());
        $this->deleteStoredImages($product->skus()->pluck('image_url')->all());
        $this->deleteStoredImage($product->image_url);

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', __('ui.messages.product_deleted'));
    }

    private function validatedData(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:5000'],
            'sales_count' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'is_active' => ['nullable', 'boolean'],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*' => ['nullable', 'string', 'max:1000'],
            'image_files' => ['nullable', 'array'],
            'image_files.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'enabled_currencies' => ['required', 'array', 'min:1'],
            'enabled_currencies.*' => ['string', Rule::in($this->currencies)],
            'prices' => ['required', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'discount_prices' => ['nullable', 'array'],
            'discount_prices.*' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'skus' => ['required', 'array'],
            'skus.*.id' => ['nullable', 'integer'],
            'skus.*.name' => ['nullable', 'string', 'max:120'],
            'skus.*.code' => ['nullable', 'string', 'max:120'],
            'skus.*.image_url' => ['nullable', 'string', 'max:1000'],
            'skus.*.stock' => ['nullable', 'integer', 'min:0'],
            'skus.*.is_active' => ['nullable', 'boolean'],
            'skus.*.remove_image' => ['nullable', 'boolean'],
            'sku_image_files' => ['nullable', 'array'],
            'sku_image_files.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $enabledCurrencies = collect(Arr::get($data, 'enabled_currencies', []))
            ->map(function ($currency) {
                return strtoupper($currency);
            })
            ->filter(function ($currency) {
                return in_array($currency, $this->currencies, true);
            })
            ->unique()
            ->values()
            ->all();

        $prices = collect($data['prices'])
            ->mapWithKeys(function ($price, $currency) {
                return [strtoupper($currency) => ['price' => $price]];
            })
            ->only($enabledCurrencies)
            ->filter(function ($priceData) {
                return $priceData['price'] !== null && $priceData['price'] !== '';
            })
            ->all();

        $discountPrices = collect(Arr::get($data, 'discount_prices', []))
            ->mapWithKeys(function ($price, $currency) {
                return [strtoupper($currency) => $price];
            })
            ->only($enabledCurrencies)
            ->all();

        $missingPrices = collect($enabledCurrencies)
            ->filter(function ($currency) use ($prices) {
                return ! isset($prices[$currency]);
            })
            ->values()
            ->all();

        if (! empty($missingPrices)) {
            $messages = [];

            foreach ($missingPrices as $currency) {
                $messages['prices.' . $currency] = __('ui.messages.price_required', ['currency' => $currency]);
            }

            throw ValidationException::withMessages($messages);
        }

        foreach ($prices as $currency => $priceData) {
            $discountPrice = $discountPrices[$currency] ?? null;

            if ($discountPrice === null || $discountPrice === '') {
                $prices[$currency]['discount_price'] = null;
                continue;
            }

            if ((float) $discountPrice >= (float) $priceData['price']) {
                throw ValidationException::withMessages([
                    'discount_prices.' . $currency => __('ui.messages.discount_price_must_be_less', ['currency' => $currency]),
                ]);
            }

            $prices[$currency]['discount_price'] = $discountPrice;
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

        $allowedSkuImages = $product
            ? $product->skus()->pluck('image_url', 'id')->all()
            : [];

        $skuImageFiles = $request->file('sku_image_files', []);

        $skus = collect(Arr::get($data, 'skus', []))
            ->map(function ($sku, $index) use ($allowedSkuImages, $skuImageFiles) {
                $name = trim($sku['name'] ?? '');

                if ($name === '') {
                    return [
                        'name' => '',
                    ];
                }

                $skuId = ! empty($sku['id']) ? (int) $sku['id'] : null;
                $imageUrl = null;

                if ($skuId && ! empty($sku['image_url']) && ($allowedSkuImages[$skuId] ?? null) === $sku['image_url']) {
                    $imageUrl = $sku['image_url'];
                }

                if (! empty($sku['remove_image'])) {
                    $imageUrl = null;
                }

                $imageFile = Arr::get($skuImageFiles, $index);

                if ($imageFile && $imageFile->isValid()) {
                    $imageUrl = $this->storeUploadedSkuImage($imageFile);
                }

                return [
                    'id' => $skuId,
                    'name' => $name,
                    'code' => trim($sku['code'] ?? '') ?: null,
                    'image_url' => $imageUrl,
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
                'skus' => __('ui.messages.sku_required'),
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
                'slug' => $product ? (string) $product->id : 'pending-' . (string) Str::uuid(),
                'description' => $data['description'],
                'price' => $prices[$enabledCurrencies[0]]['discount_price'] ?: $prices[$enabledCurrencies[0]]['price'],
                'stock' => $activeStock,
                'sales_count' => (int) ($data['sales_count'] ?? 0),
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
            $storedImages[] = $this->publicStorageUrl($path);
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

        if (
            ! Str::startsWith($storagePath, 'product-images/')
            && ! Str::startsWith($storagePath, 'sku-images/')
        ) {
            return;
        }

        Storage::disk('public')->delete($storagePath);
    }

    private function storeUploadedSkuImage($file): string
    {
        $path = $file->store('sku-images', 'public');

        return $this->publicStorageUrl($path);
    }

    private function publicStorageUrl(string $path): string
    {
        return '/storage/' . ltrim($path, '/');
    }

    private function syncPrices(Product $product, array $prices): void
    {
        $product->prices()->delete();

        foreach ($prices as $currency => $priceData) {
            $product->prices()->create([
                'currency_code' => $currency,
                'price' => $priceData['price'],
                'discount_price' => $priceData['discount_price'],
            ]);
        }
    }

    private function syncSkus(Product $product, array $skus): void
    {
        $existingSkus = $product->skus()->get()->keyBy('id');
        $incomingExistingIds = collect($skus)
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        $removedSkus = $product->skus()
            ->when(! empty($incomingExistingIds), function ($query) use ($incomingExistingIds) {
                $query->whereNotIn('id', $incomingExistingIds);
            })
            ->get();

        foreach ($removedSkus as $removedSku) {
            $this->deleteStoredImage($removedSku->image_url);
            $removedSku->delete();
        }

        foreach ($skus as $index => $sku) {
            $existingSku = $sku['id'] ? $existingSkus->get($sku['id']) : null;
            $attributes = [
                'name' => $sku['name'],
                'code' => $sku['code'],
                'image_url' => $sku['image_url'],
                'stock' => $sku['stock'],
                'sort_order' => $index,
                'is_active' => $sku['is_active'],
            ];

            if ($existingSku) {
                if ($existingSku->image_url && $existingSku->image_url !== $sku['image_url']) {
                    $this->deleteStoredImage($existingSku->image_url);
                }

                $existingSku->update($attributes);

                continue;
            }

            $product->skus()->create($attributes);
        }

        $product->update([
            'stock' => $product->activeSkus()->sum('stock'),
        ]);
    }
}
