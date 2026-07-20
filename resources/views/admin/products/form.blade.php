@extends('admin.partials.shell')

@section('title', $product->exists ? __('ui.admin.edit_product') : __('ui.admin.add_product'))

@section('content')
    @php
        $priceMap = $product->exists ? $product->prices->pluck('price', 'currency_code')->all() : [];
        $discountPriceMap = $product->exists ? $product->prices->pluck('discount_price', 'currency_code')->all() : [];
        $selectedCurrencies = old('enabled_currencies');
        if ($selectedCurrencies === null) {
            $selectedCurrencies = $product->exists ? array_keys($priceMap) : [];
        }
        $selectedCurrencies = array_values(array_intersect($currencies, array_map('strtoupper', (array) $selectedCurrencies)));
        $imageList = $product->exists ? $product->images : collect();
        $skuList = old('skus', $product->exists ? $product->skus->map(function ($sku) {
            return [
                'id' => $sku->id,
                'name' => $sku->name,
                'code' => $sku->code,
                'image_url' => $sku->image_url,
                'stock' => $sku->stock,
                'is_active' => $sku->is_active ? 1 : 0,
            ];
        })->all() : []);
        $emptySku = ['id' => null, 'name' => '', 'code' => '', 'image_url' => null, 'stock' => 0, 'is_active' => 1];
        $skuRows = count($skuList) ? $skuList : [$emptySku];
    @endphp

    <div class="page-head">
        <h1>{{ $product->exists ? __('ui.admin.edit_product') : __('ui.admin.add_product') }}</h1>
        <a class="button secondary" href="{{ route('admin.products.index') }}">{{ __('ui.common.back') }}</a>
    </div>

    <form class="panel" method="post" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($product->exists)
            @method('put')
        @endif

        <div class="form-grid">
            <div>
                <label for="category_id">{{ __('ui.admin.product_category') }}</label>
                <select id="category_id" name="category_id" required>
                    <option value="">{{ __('ui.common.select') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) old('category_id', $product->category_id) === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="name">{{ __('ui.admin.product_name') }}</label>
                <input id="name" name="name" value="{{ old('name', $product->name) }}" required>
            </div>

            <div>
                <label>{{ __('ui.admin.product_total_stock') }}</label>
                <input value="{{ $product->exists ? $product->stock : __('ui.admin.stock_auto_hint') }}" disabled>
            </div>

            <div>
                <label for="sales_count">{{ __('ui.admin.sales') }}</label>
                <input id="sales_count" type="number" min="0" step="1" name="sales_count" value="{{ old('sales_count', $product->sales_count ?? 0) }}">
            </div>

            <div class="full">
                <label for="description">{{ __('ui.admin.product_description') }}</label>
                <textarea id="description" name="description" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="full">
                <h2>{{ __('ui.admin.multi_currency_price') }}</h2>
                <div class="currency-selector">
                    @foreach ($currencies as $currency)
                        @php
                            $isSelected = in_array($currency, $selectedCurrencies, true);
                        @endphp
                        <label class="currency-option {{ $isSelected ? 'is-selected' : '' }}" data-currency-option>
                            <input
                                type="checkbox"
                                name="enabled_currencies[]"
                                value="{{ $currency }}"
                                data-currency-toggle
                                {{ $isSelected ? 'checked' : '' }}
                                style="width: auto;"
                            >
                            <span>{{ $currency }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="inline-grid currency-price-grid">
                    @foreach ($currencies as $currency)
                        @php
                            $isSelected = in_array($currency, $selectedCurrencies, true);
                        @endphp
                        <div class="currency-price-card" data-currency-price="{{ $currency }}" {{ $isSelected ? '' : 'hidden' }}>
                            <h3>{{ $currency }}</h3>
                            <div class="currency-price-fields">
                                <div>
                                    <label for="price_{{ $currency }}">{{ __('ui.admin.regular_price') }}</label>
                                    <input
                                        id="price_{{ $currency }}"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        name="prices[{{ $currency }}]"
                                        value="{{ old('prices.' . $currency, $priceMap[$currency] ?? '') }}"
                                        data-regular-price
                                        {{ $isSelected ? 'required' : 'disabled' }}
                                    >
                                </div>
                                <div>
                                    <label for="discount_price_{{ $currency }}">{{ __('ui.admin.discount_price') }}</label>
                                    <input
                                        id="discount_price_{{ $currency }}"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        name="discount_prices[{{ $currency }}]"
                                        value="{{ old('discount_prices.' . $currency, $discountPriceMap[$currency] ?? '') }}"
                                        {{ $isSelected ? '' : 'disabled' }}
                                    >
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="full">
                <div class="section-head">
                    <h2>{{ __('ui.admin.sku_stock') }}</h2>
                    <button class="button secondary" type="button" data-add-sku-row>{{ __('ui.admin.add_sku') }}</button>
                </div>
                <p class="muted">{{ __('ui.admin.sku_hint') }}</p>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('ui.admin.sku_name') }}</th>
                                <th>{{ __('ui.admin.sku_code') }}</th>
                                <th>{{ __('ui.admin.sku_image') }}</th>
                                <th>{{ __('ui.common.stock') }}</th>
                                <th>{{ __('ui.admin.enabled') }}</th>
                            </tr>
                        </thead>
                        <tbody data-sku-rows>
                            @foreach ($skuRows as $index => $sku)
                                @include('admin.products.partials.sku-row', ['index' => $index, 'sku' => $sku])
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <template data-sku-row-template>
                    @include('admin.products.partials.sku-row', ['index' => '__INDEX__', 'sku' => $emptySku])
                </template>
            </div>

            <div class="full">
                <h2>{{ __('ui.admin.product_images') }}</h2>
                <p class="muted">{{ __('ui.admin.image_hint') }}</p>

                @if ($imageList->count())
                    <div class="table-wrap" style="margin-bottom: 14px;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('ui.admin.preview') }}</th>
                                    <th>{{ __('ui.admin.image_path') }}</th>
                                    <th>{{ __('ui.admin.keep') }}</th>
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
                                                {{ __('ui.admin.keep') }}
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <label for="image_files">{{ __('ui.admin.upload_new_images') }}</label>
                <div class="image-uploader-shell">
                    <input
                        id="image_files"
                        type="file"
                        name="image_files[]"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        multiple
                        data-filepond-upload
                    >
                </div>
            </div>

            <div class="full">
                <label style="align-items: center; display: flex; gap: 8px; font-weight: 400;">
                    <input type="checkbox" name="is_active" value="1" style="width: auto;" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    {{ __('ui.admin.active') }}
                </label>
            </div>
        </div>

        <div style="margin-top: 18px;">
            <button class="button" type="submit">{{ __('ui.admin.save_product') }}</button>
        </div>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/filepond@4/dist/filepond.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">

    <style>
        .currency-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }
        .currency-option {
            align-items: center;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            gap: 8px;
            margin: 0;
            min-height: 42px;
            padding: 9px 12px;
        }
        .currency-option.is-selected {
            background: #ecfdf5;
            border-color: var(--brand);
            color: var(--brand-dark);
        }
        .currency-option input:disabled {
            cursor: not-allowed;
        }
        .currency-price-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 6px;
        }
        .currency-price-card {
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px;
        }
        .currency-price-card h3 {
            font-size: 15px;
            margin: 0 0 10px;
        }
        .currency-price-fields {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .section-head {
            align-items: center;
            display: flex;
            gap: 12px;
            justify-content: space-between;
        }
        .section-head h2 {
            margin-bottom: 0;
        }
        .sku-image-field {
            display: grid;
            gap: 8px;
            min-width: 132px;
        }
        .sku-image-picker {
            align-items: center;
            background: #f8fafc;
            border: 1px dashed #94a3b8;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            height: 92px;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            position: relative;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            width: 112px;
        }
        .sku-image-picker:hover {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
        }
        .sku-image-picker input {
            inset: 0;
            cursor: pointer;
            opacity: 0;
            position: absolute;
            width: 100%;
        }
        .sku-image-preview {
            background: #ffffff;
            height: 100%;
            object-fit: cover;
            width: 100%;
        }
        .sku-image-empty {
            align-items: center;
            color: var(--muted);
            display: flex;
            font-size: 12px;
            justify-content: center;
            padding: 10px;
            text-align: center;
            width: 100%;
        }
        .sku-remove-image {
            align-items: center;
            display: flex;
            gap: 6px;
            font-size: 13px;
            font-weight: 400;
            margin: 0;
        }
        .sku-remove-image input {
            width: auto;
        }
        .sku-actions-cell {
            display: grid;
            gap: 8px;
            min-width: 110px;
        }
        .sku-row-remove {
            min-height: 34px;
            padding: 6px 10px;
        }
        .image-uploader-shell {
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px;
        }
        .filepond--root {
            font-family: inherit;
            margin-bottom: 0;
        }
        .filepond--list {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        }
        .filepond--item {
            margin: 0;
            max-width: 220px;
            width: 100%;
        }
        .filepond--panel-root {
            background: #ffffff;
            border: 1px dashed #94a3b8;
            border-radius: 8px;
        }
        .filepond--drop-label {
            color: var(--ink);
            min-height: 150px;
        }
        .filepond-note {
            color: var(--muted);
            display: inline-block;
            font-size: 13px;
            margin-top: 6px;
        }
        .filepond--label-action {
            color: var(--brand);
            font-weight: 700;
            text-decoration: none;
        }
        .filepond--item-panel {
            background: #0f766e;
        }
        .filepond--image-preview {
            height: 120px;
        }
        .filepond--credits {
            display: none;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/filepond@4/dist/filepond.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-exif-orientation@1/dist/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-size@2/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.querySelector('[data-filepond-upload]');
            var currencyToggles = document.querySelectorAll('[data-currency-toggle]');
            var skuRows = document.querySelector('[data-sku-rows]');
            var addSkuRowButton = document.querySelector('[data-add-sku-row]');
            var skuRowTemplate = document.querySelector('[data-sku-row-template]');

            function syncCurrencyField(toggle) {
                var priceWrap = document.querySelector('[data-currency-price="' + toggle.value + '"]');
                var option = toggle.closest('[data-currency-option]');
                var priceInputs = priceWrap ? priceWrap.querySelectorAll('input') : [];
                var regularPriceInput = priceWrap ? priceWrap.querySelector('[data-regular-price]') : null;
                var enabled = toggle.checked;

                if (priceWrap) {
                    priceWrap.hidden = !enabled;
                }

                priceInputs.forEach(function (priceInput) {
                    priceInput.disabled = !enabled;
                });

                if (regularPriceInput) {
                    regularPriceInput.required = enabled;
                }

                if (option) {
                    option.classList.toggle('is-selected', enabled);
                }
            }

            currencyToggles.forEach(function (toggle) {
                syncCurrencyField(toggle);
                toggle.addEventListener('change', function () {
                    syncCurrencyField(toggle);
                });
            });

            function renumberSkuRows() {
                if (!skuRows) {
                    return;
                }

                Array.prototype.slice.call(skuRows.querySelectorAll('[data-sku-row]')).forEach(function (row, index) {
                    row.querySelectorAll('[data-sku-name]').forEach(function (field) {
                        field.name = 'skus[' + index + '][' + field.dataset.skuName + ']';
                    });
                    row.querySelectorAll('[data-sku-file]').forEach(function (field) {
                        field.name = 'sku_image_files[' + index + ']';
                    });
                });
            }

            function bindSkuImageInput(skuImageInput) {
                if (skuImageInput.dataset.bound === '1') {
                    return;
                }

                skuImageInput.dataset.bound = '1';
                skuImageInput.addEventListener('change', function () {
                    var file = skuImageInput.files && skuImageInput.files[0] ? skuImageInput.files[0] : null;
                    var field = skuImageInput.closest('.sku-image-field');
                    var preview = field ? field.querySelector('.sku-image-preview') : null;
                    var empty = field ? field.querySelector('.sku-image-empty') : null;

                    if (!file || !field) {
                        return;
                    }

                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'sku-image-preview';
                        preview.alt = '';
                        skuImageInput.closest('.sku-image-picker').insertBefore(preview, skuImageInput);
                    }

                    if (empty) {
                        empty.remove();
                    }

                    preview.src = URL.createObjectURL(file);
                });
            }

            function bindSkuRow(row) {
                var imageInput = row.querySelector('[data-sku-image-input]');

                if (imageInput) {
                    bindSkuImageInput(imageInput);
                }
            }

            if (skuRows) {
                skuRows.querySelectorAll('[data-sku-row]').forEach(bindSkuRow);
                skuRows.addEventListener('click', function (event) {
                    var removeButton = event.target.closest('[data-remove-sku-row]');

                    if (!removeButton) {
                        return;
                    }

                    var rows = skuRows.querySelectorAll('[data-sku-row]');

                    if (rows.length <= 1) {
                        return;
                    }

                    removeButton.closest('[data-sku-row]').remove();
                    renumberSkuRows();
                });
            }

            if (addSkuRowButton && skuRows && skuRowTemplate) {
                addSkuRowButton.addEventListener('click', function () {
                    var nextIndex = skuRows.querySelectorAll('[data-sku-row]').length;
                    var html = skuRowTemplate.innerHTML.replace(/__INDEX__/g, nextIndex);
                    var wrapper = document.createElement('tbody');
                    wrapper.innerHTML = html.trim();
                    var row = wrapper.querySelector('[data-sku-row]');

                    if (!row) {
                        return;
                    }

                    skuRows.appendChild(row);
                    bindSkuRow(row);
                    renumberSkuRows();
                    row.querySelector('input[data-sku-name="name"]').focus();
                });
            }

            if (!input || !window.FilePond) {
                return;
            }

            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageExifOrientation,
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize
            );

            FilePond.create(input, {
                acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                allowImagePreview: true,
                allowMultiple: true,
                allowReorder: true,
                credits: false,
                imagePreviewHeight: 120,
                maxFileSize: '5MB',
                storeAsFile: true,
                labelIdle: '<strong>' + @json(__('ui.admin.click_select_images')) + '</strong><br><span class="filepond-note">' + @json(__('ui.admin.mobile_upload_hint')) + '</span>',
                labelFileTypeNotAllowed: @json(__('ui.admin.only_image_files')),
                fileValidateTypeLabelExpectedTypes: @json(__('ui.admin.supported_image_types')),
                labelMaxFileSizeExceeded: @json(__('ui.admin.image_too_large')),
                labelMaxFileSize: @json(__('ui.admin.single_image_max')),
                labelFileLoading: @json(__('ui.admin.file_loading')),
                labelFileProcessing: @json(__('ui.admin.preparing_upload')),
                labelFileProcessingComplete: @json(__('ui.admin.added_upload_list')),
                labelTapToCancel: @json(__('ui.admin.tap_to_cancel')),
                labelTapToRetry: @json(__('ui.admin.tap_to_retry')),
                labelTapToUndo: @json(__('ui.admin.tap_to_undo')),
                labelButtonRemoveItem: @json(__('ui.common.remove')),
                labelButtonAbortItemLoad: @json(__('ui.common.cancel')),
                labelButtonRetryItemLoad: @json(__('ui.admin.tap_to_retry')),
                labelButtonAbortItemProcessing: @json(__('ui.common.cancel')),
                labelButtonUndoItemProcessing: @json(__('ui.admin.tap_to_undo')),
                labelButtonRetryItemProcessing: @json(__('ui.admin.tap_to_retry')),
                labelButtonProcessItem: @json(__('ui.admin.upload'))
            });
        });
    </script>
@endsection
