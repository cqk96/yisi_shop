@extends('layouts.app')

@section('title', $product->name . ' - LaravelShop')

@section('content')
    <style>
        .product-gallery {
            max-width: 720px;
            position: relative;
            width: 100%;
        }
        .zoom-stage {
            align-items: center;
            aspect-ratio: 1 / 1;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            cursor: crosshair;
            display: flex;
            justify-content: center;
            max-height: 640px;
            min-height: 420px;
            overflow: hidden;
            position: relative;
        }
        .zoom-stage img {
            display: block;
            height: 100%;
            object-fit: contain;
            width: 100%;
        }
        .gallery-arrow {
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--line);
            border-radius: 999px;
            color: var(--ink);
            cursor: pointer;
            display: flex;
            font-size: 30px;
            height: 44px;
            justify-content: center;
            line-height: 1;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            z-index: 20;
        }
        .gallery-arrow:hover {
            background: #ffffff;
            border-color: var(--brand);
            color: var(--brand-dark);
        }
        .gallery-arrow.prev { left: 14px; }
        .gallery-arrow.next { right: 14px; }
        .zoom-lens {
            background: rgba(255, 255, 255, 0.28);
            border: 1px solid rgba(15, 118, 110, 0.75);
            display: none;
            left: 0;
            pointer-events: none;
            position: absolute;
            top: 0;
        }
        .zoom-preview {
            aspect-ratio: 1 / 1;
            background-color: #ffffff;
            background-repeat: no-repeat;
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 16px 36px rgba(31, 41, 55, 0.16);
            display: none;
            left: calc(100% + 16px);
            max-height: 640px;
            position: absolute;
            top: 0;
            width: min(460px, 34vw);
            z-index: 30;
        }
        .zoom-stage.is-active .zoom-lens,
        .product-gallery.is-active .zoom-preview {
            display: block;
        }
        .thumbnail-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            margin-top: 12px;
        }
        .thumbnail-grid img {
            aspect-ratio: 4 / 3;
            border: 2px solid transparent;
            cursor: pointer;
            object-fit: cover;
        }
        .thumbnail-grid img.is-active {
            border-color: var(--brand);
        }
        .sku-options {
            display: grid;
            gap: 10px;
            margin: 10px 0 14px;
        }
        .sku-option {
            align-items: center;
            border: 1px solid var(--line);
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            gap: 10px;
            justify-content: space-between;
            padding: 10px 12px;
        }
        .sku-option input {
            width: auto;
        }
        .sku-option:has(input:checked) {
            border-color: var(--brand);
            box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.14);
        }
        @media (max-width: 980px) {
            .zoom-preview,
            .zoom-lens {
                display: none !important;
            }
            .zoom-stage {
                cursor: default;
                min-height: 320px;
            }
        }
        @media (max-width: 560px) {
            .zoom-stage {
                min-height: 260px;
            }
            .gallery-arrow {
                height: 38px;
                width: 38px;
            }
        }
    </style>

    @php
        $firstSku = $product->activeSkus->first();
        $galleryImages = $product->images->count()
            ? $product->images
            : collect([(object) ['image_url' => $product->primaryImage(), 'alt_text' => $product->name]]);
        $displayCurrency = $product->displayCurrency();
        $displayPrice = $product->displayPrice();
    @endphp

    <div class="detail">
        <div class="product-gallery" data-zoom-gallery>
            <div class="zoom-stage" data-zoom-stage>
                <img src="{{ $galleryImages->first()->image_url }}" alt="{{ $galleryImages->first()->alt_text ?: $product->name }}" data-zoom-image>
                @if ($galleryImages->count() > 1)
                    <button class="gallery-arrow prev" type="button" data-gallery-prev aria-label="{{ __('ui.shop.previous_image') }}">&lsaquo;</button>
                    <button class="gallery-arrow next" type="button" data-gallery-next aria-label="{{ __('ui.shop.next_image') }}">&rsaquo;</button>
                @endif
                <div class="zoom-lens" data-zoom-lens></div>
            </div>
            <div class="zoom-preview" data-zoom-preview aria-hidden="true"></div>

            @if ($galleryImages->count() > 1)
                <div class="thumbnail-grid">
                    @foreach ($galleryImages as $image)
                        <img
                            class="{{ $loop->first ? 'is-active' : '' }}"
                            src="{{ $image->image_url }}"
                            alt="{{ $image->alt_text ?: $product->name }}"
                            data-zoom-thumb
                            data-gallery-index="{{ $loop->index }}"
                        >
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="panel">
            <p class="muted">{{ $product->category->name }}</p>
            <h1>{{ $product->name }}</h1>
            <p>{{ $product->description }}</p>
            <p class="price">{{ $displayCurrency }} {{ number_format($displayPrice, 2) }}</p>

            @if ($product->prices->whereIn('currency_code', ['USD', 'HKD', 'CUP'])->count() > 1)
                <p class="muted">
                    @foreach ($product->prices->whereIn('currency_code', ['USD', 'HKD', 'CUP']) as $price)
                        {{ $price->currency_code }} {{ number_format($price->price, 2) }}{{ $loop->last ? '' : ' / ' }}
                    @endforeach
                </p>
            @endif

            <form method="post" action="{{ route('cart.items.store') }}">
                @csrf
                <label>{{ __('ui.shop.select_sku') }}</label>
                <div class="sku-options">
                    @forelse ($product->activeSkus as $sku)
                        <label class="sku-option">
                            <span>
                                <input
                                    type="radio"
                                    name="sku_id"
                                    value="{{ $sku->id }}"
                                    data-stock="{{ $sku->stock }}"
                                    {{ $loop->first ? 'checked' : '' }}
                                    {{ $sku->stock <= 0 ? 'disabled' : '' }}
                                    required
                                >
                                {{ $sku->name }}
                                @if ($sku->code)
                                    <span class="muted">({{ $sku->code }})</span>
                                @endif
                            </span>
                            <span class="muted">{{ __('ui.common.stock') }} {{ $sku->stock }}</span>
                        </label>
                    @empty
                        <div class="alert error">{{ __('ui.shop.no_salable_sku') }}</div>
                    @endforelse
                </div>

                <p class="muted">{{ __('ui.shop.current_sku_stock') }} <span data-selected-stock>{{ $firstSku ? $firstSku->stock : 0 }}</span></p>
                <label for="quantity">{{ __('ui.shop.purchase_quantity') }}</label>
                <input id="quantity" type="number" name="quantity" min="1" max="{{ $firstSku ? max(1, $firstSku->stock) : 1 }}" value="1" style="margin: 8px 0 14px;">
                <button class="button" type="submit" {{ ! $firstSku || $firstSku->stock <= 0 ? 'disabled' : '' }}>{{ __('ui.common.add_to_cart') }}</button>
                <a class="button secondary" href="{{ route('shop.index') }}">{{ __('ui.common.back_to_list') }}</a>
            </form>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var gallery = document.querySelector('[data-zoom-gallery]');

            if (gallery) {
                var stage = gallery.querySelector('[data-zoom-stage]');
                var image = gallery.querySelector('[data-zoom-image]');
                var lens = gallery.querySelector('[data-zoom-lens]');
                var preview = gallery.querySelector('[data-zoom-preview]');
                var thumbs = Array.prototype.slice.call(gallery.querySelectorAll('[data-zoom-thumb]'));
                var prevButton = gallery.querySelector('[data-gallery-prev]');
                var nextButton = gallery.querySelector('[data-gallery-next]');
                var zoom = 2.6;
                var currentIndex = 0;

                function setPreviewImage(src) {
                    preview.style.backgroundImage = 'url("' + src + '")';
                }

                function setActiveImage(index) {
                    if (!thumbs.length) {
                        return;
                    }

                    currentIndex = (index + thumbs.length) % thumbs.length;
                    var thumb = thumbs[currentIndex];
                    image.src = thumb.src;
                    image.alt = thumb.alt;
                    setPreviewImage(thumb.src);

                    thumbs.forEach(function (item) {
                        item.classList.remove('is-active');
                    });
                    thumb.classList.add('is-active');
                }

                function imageContentRect() {
                    var rect = image.getBoundingClientRect();
                    var naturalRatio = image.naturalWidth / image.naturalHeight;
                    var boxRatio = rect.width / rect.height;
                    var width = rect.width;
                    var height = rect.height;
                    var left = rect.left;
                    var top = rect.top;

                    if (naturalRatio > boxRatio) {
                        height = rect.width / naturalRatio;
                        top = rect.top + (rect.height - height) / 2;
                    } else {
                        width = rect.height * naturalRatio;
                        left = rect.left + (rect.width - width) / 2;
                    }

                    return { left: left, top: top, width: width, height: height };
                }

                function moveZoom(event) {
                    var imageRect = imageContentRect();
                    var stageRect = stage.getBoundingClientRect();
                    var previewRect = preview.getBoundingClientRect();
                    var x = Math.max(0, Math.min(event.clientX - imageRect.left, imageRect.width));
                    var y = Math.max(0, Math.min(event.clientY - imageRect.top, imageRect.height));
                    var lensWidth = Math.max(70, previewRect.width / zoom);
                    var lensHeight = Math.max(70, previewRect.height / zoom);
                    var lensLeft = Math.max(0, Math.min(x - lensWidth / 2, imageRect.width - lensWidth));
                    var lensTop = Math.max(0, Math.min(y - lensHeight / 2, imageRect.height - lensHeight));
                    var backgroundX = Math.max(0, Math.min(x * zoom - previewRect.width / 2, imageRect.width * zoom - previewRect.width));
                    var backgroundY = Math.max(0, Math.min(y * zoom - previewRect.height / 2, imageRect.height * zoom - previewRect.height));

                    lens.style.width = lensWidth + 'px';
                    lens.style.height = lensHeight + 'px';
                    lens.style.left = (lensLeft + imageRect.left - stageRect.left) + 'px';
                    lens.style.top = (lensTop + imageRect.top - stageRect.top) + 'px';
                    preview.style.backgroundSize = (imageRect.width * zoom) + 'px ' + (imageRect.height * zoom) + 'px';
                    preview.style.backgroundPosition = '-' + backgroundX + 'px -' + backgroundY + 'px';
                }

                stage.addEventListener('mouseenter', function (event) {
                    setPreviewImage(image.currentSrc || image.src);
                    gallery.classList.add('is-active');
                    stage.classList.add('is-active');
                    moveZoom(event);
                });
                stage.addEventListener('mousemove', moveZoom);
                stage.addEventListener('mouseleave', function () {
                    gallery.classList.remove('is-active');
                    stage.classList.remove('is-active');
                });
                thumbs.forEach(function (thumb, index) {
                    thumb.addEventListener('click', function () {
                        setActiveImage(index);
                    });
                });
                if (prevButton) {
                    prevButton.addEventListener('click', function () {
                        setActiveImage(currentIndex - 1);
                    });
                }
                if (nextButton) {
                    nextButton.addEventListener('click', function () {
                        setActiveImage(currentIndex + 1);
                    });
                }
            }

            var quantity = document.querySelector('#quantity');
            var stockText = document.querySelector('[data-selected-stock]');
            var skuRadios = document.querySelectorAll('input[name="sku_id"]');

            skuRadios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    var stock = parseInt(radio.dataset.stock || '0', 10);
                    quantity.max = Math.max(1, stock);
                    quantity.value = Math.min(parseInt(quantity.value || '1', 10), Math.max(1, stock));
                    stockText.textContent = stock;
                });
            });
        });
    </script>
@endsection
