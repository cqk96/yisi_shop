@extends('admin.partials.shell')

@section('title', $product->exists ? '编辑商品' : '新增商品')

@section('content')
    @php
        $priceMap = $product->exists ? $product->prices->pluck('price', 'currency_code')->all() : [];
        $imageList = $product->exists ? $product->images : collect();
        $skuList = old('skus', $product->exists ? $product->skus->map(function ($sku) {
            return [
                'name' => $sku->name,
                'code' => $sku->code,
                'stock' => $sku->stock,
                'is_active' => $sku->is_active ? 1 : 0,
            ];
        })->all() : []);
        $skuRows = array_pad($skuList, 6, ['name' => '', 'code' => '', 'stock' => 0, 'is_active' => 1]);
    @endphp

    <div class="page-head">
        <h1>{{ $product->exists ? '编辑商品' : '新增商品' }}</h1>
        <a class="button secondary" href="{{ route('admin.products.index') }}">返回</a>
    </div>

    <form class="panel" method="post" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($product->exists)
            @method('put')
        @endif

        <div class="form-grid">
            <div>
                <label for="category_id">商品分类</label>
                <select id="category_id" name="category_id" required>
                    <option value="">请选择分类</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) old('category_id', $product->category_id) === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="name">商品名称</label>
                <input id="name" name="name" value="{{ old('name', $product->name) }}" required>
            </div>

            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="留空自动生成">
            </div>

            <div>
                <label>商品总库存</label>
                <input value="{{ $product->exists ? $product->stock : '保存后自动按启用 SKU 汇总' }}" disabled>
            </div>

            <div class="full">
                <label for="description">商品描述</label>
                <textarea id="description" name="description" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="full">
                <h2>多币种价格</h2>
                <div class="inline-grid">
                    @foreach ($currencies as $currency)
                        <div>
                            <label for="price_{{ $currency }}">{{ $currency }}</label>
                            <input id="price_{{ $currency }}" type="number" min="0" step="0.01" name="prices[{{ $currency }}]" value="{{ old('prices.' . $currency, $priceMap[$currency] ?? ($currency === 'CNY' ? $product->price : '')) }}" {{ $currency === 'CNY' ? 'required' : '' }}>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="full">
                <h2>SKU 库存</h2>
                <p class="muted">每个 SKU 独立库存；商品总库存会自动汇总启用 SKU 的库存。</p>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SKU 名称</th>
                                <th>SKU 编码</th>
                                <th>库存</th>
                                <th>启用</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($skuRows as $index => $sku)
                                <tr>
                                    <td><input name="skus[{{ $index }}][name]" value="{{ $sku['name'] ?? '' }}" placeholder="如：红色 / 30ml / A 款"></td>
                                    <td><input name="skus[{{ $index }}][code]" value="{{ $sku['code'] ?? '' }}" placeholder="如：SKU-RED-001"></td>
                                    <td><input type="number" min="0" name="skus[{{ $index }}][stock]" value="{{ $sku['stock'] ?? 0 }}"></td>
                                    <td>
                                        <label style="align-items: center; display: flex; gap: 8px; font-weight: 400;">
                                            <input type="checkbox" name="skus[{{ $index }}][is_active]" value="1" style="width: auto;" {{ ! empty($sku['is_active']) ? 'checked' : '' }}>
                                            上架
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="full">
                <h2>商品图片</h2>
                <p class="muted">图片上传到服务器保存。取消保留图片或删除商品时，服务器上的本地图片文件会自动删除。</p>

                @if ($imageList->count())
                    <div class="table-wrap" style="margin-bottom: 14px;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>预览</th>
                                    <th>图片路径</th>
                                    <th>保留</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($imageList as $image)
                                    <tr>
                                        <td>
                                            <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}" style="height: 70px; object-fit: cover; width: 90px;">
                                        </td>
                                        <td class="muted">{{ $image->image_url }}</td>
                                        <td>
                                            <label style="align-items: center; display: flex; gap: 8px; font-weight: 400;">
                                                <input type="checkbox" name="existing_images[]" value="{{ $image->image_url }}" checked style="width: auto;">
                                                保留
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <label for="image_files">上传新图片</label>
                <input id="image_files" type="file" name="image_files[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
            </div>

            <div class="full">
                <label style="align-items: center; display: flex; gap: 8px; font-weight: 400;">
                    <input type="checkbox" name="is_active" value="1" style="width: auto;" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    商品上架
                </label>
            </div>
        </div>

        <div style="margin-top: 18px;">
            <button class="button" type="submit">保存商品</button>
        </div>
    </form>
@endsection
