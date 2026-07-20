@extends('layouts.app')

@section('title', __('ui.shop.title') . ' - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ __('ui.shop.title') }}</h1>
            <p class="muted">{{ __('ui.shop.subtitle') }}</p>
        </div>
        <form class="search" method="get" action="{{ route('shop.index') }}">
            @if ($selectedCategory)
                <input type="hidden" name="category" value="{{ $selectedCategory }}">
            @endif
            <input type="search" name="q" value="{{ $keyword }}" placeholder="{{ __('ui.shop.search_placeholder') }}">
            <button class="button" type="submit">{{ __('ui.common.search') }}</button>
        </form>
    </div>

    <div class="toolbar">
        <div class="chips">
            <a class="chip {{ $selectedCategory ? '' : 'active' }}" href="{{ route('shop.index', ['q' => $keyword]) }}">{{ __('ui.shop.all') }}</a>
            @foreach ($categories as $category)
                <a class="chip {{ $selectedCategory === $category->slug ? 'active' : '' }}" href="{{ route('shop.index', ['category' => $category->slug, 'q' => $keyword]) }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid product-grid product-grid-count-{{ $products->count() }}">
        @forelse ($products as $product)
            @php
                $firstSku = $product->activeSkus->first();
                $stock = $product->availableStock();
                $visiblePrices = $product->prices
                    ->whereIn('currency_code', ['USD', 'CUP'])
                    ->sortBy(function ($price) {
                        return ['USD' => 0, 'CUP' => 1][$price->currency_code] ?? 99;
                    });
            @endphp
            <article class="card">
                <a href="{{ route('products.show', $product) }}">
                    <img class="product-image" src="{{ $product->primaryImage() }}" alt="{{ $product->name }}">
                </a>
                <div class="card-body">
                    <h2 style="font-size: 19px; margin-bottom: 8px;">
                        <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                    </h2>
                    <p class="muted">{{ $product->category->name }}</p>
                    <div class="product-meta">
                        <span class="price price-stack">
                            @forelse ($visiblePrices as $price)
                                <span>
                                    {{ $price->currency_code }} {{ number_format($price->effectivePrice(), 2) }}
                                    @if ($price->hasDiscount())
                                        <span class="original-price">{{ number_format($price->price, 2) }}</span>
                                    @endif
                                </span>
                            @empty
                                <span>{{ $product->displayCurrency() }} {{ number_format($product->displayPrice(), 2) }}</span>
                            @endforelse
                        </span>
                        <span class="muted">{{ __('ui.common.stock') }} {{ $stock }}</span>
                    </div>
                    @if ($firstSku)
{{--                        <form class="product-action" method="post" action="{{ route('cart.items.store') }}">--}}
{{--                            @csrf--}}
{{--                            <input type="hidden" name="sku_id" value="{{ $firstSku->id }}">--}}
{{--                            <input type="hidden" name="quantity" value="1">--}}
{{--                            <button class="button" type="submit" {{ $stock <= 0 ? 'disabled' : '' }}>{{ __('ui.common.add_to_cart') }}</button>--}}
{{--                        </form>--}}
                    @else
                        <a class="button secondary product-action" href="{{ route('products.show', $product) }}">{{ __('ui.shop.choose_sku') }}</a>
                    @endif
                    <div class="product-sales">{{ __('ui.shop.sales') }} {{ number_format($product->sales_count) }}</div>
                </div>
            </article>
        @empty
            <div class="panel">{{ __('ui.shop.empty_products') }}</div>
        @endforelse
    </div>

    @if ($products->hasPages())
        <nav class="pager" aria-label="{{ __('ui.shop.title') }}">
            @if ($products->onFirstPage())
                <span class="pager-link disabled">{{ __('pagination.previous') }}</span>
            @else
                <a class="pager-link" href="{{ $products->previousPageUrl() }}">{{ __('pagination.previous') }}</a>
            @endif

            @for ($page = 1; $page <= $products->lastPage(); $page++)
                @if ($page === $products->currentPage())
                    <span class="pager-link active">{{ $page }}</span>
                @else
                    <a class="pager-link" href="{{ $products->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($products->hasMorePages())
                <a class="pager-link" href="{{ $products->nextPageUrl() }}">{{ __('pagination.next') }}</a>
            @else
                <span class="pager-link disabled">{{ __('pagination.next') }}</span>
            @endif
        </nav>
    @endif
@endsection
