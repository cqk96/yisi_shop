@extends('layouts.app')

@section('title', '商品列表 - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>商品列表</h1>
            <p class="muted">浏览商品、选择规格并加入购物车。</p>
        </div>
        <form class="search" method="get" action="{{ route('shop.index') }}">
            @if ($selectedCategory)
                <input type="hidden" name="category" value="{{ $selectedCategory }}">
            @endif
            <input type="search" name="q" value="{{ $keyword }}" placeholder="搜索商品">
            <button class="button" type="submit">搜索</button>
        </form>
    </div>

    <div class="toolbar">
        <div class="chips">
            <a class="chip {{ $selectedCategory ? '' : 'active' }}" href="{{ route('shop.index', ['q' => $keyword]) }}">全部</a>
            @foreach ($categories as $category)
                <a class="chip {{ $selectedCategory === $category->slug ? 'active' : '' }}" href="{{ route('shop.index', ['category' => $category->slug, 'q' => $keyword]) }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid">
        @forelse ($products as $product)
            @php
                $firstSku = $product->activeSkus->first();
                $stock = $product->availableStock();
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
                        <span class="price">&yen;{{ number_format($product->priceFor('CNY'), 2) }}</span>
                        <span class="muted">SKU 库存 {{ $stock }}</span>
                    </div>
                    @if ($firstSku)
                        <form method="post" action="{{ route('cart.items.store') }}">
                            @csrf
                            <input type="hidden" name="sku_id" value="{{ $firstSku->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="button" type="submit" {{ $stock <= 0 ? 'disabled' : '' }}>加入购物车</button>
                        </form>
                    @else
                        <a class="button secondary" href="{{ route('products.show', $product) }}">选择规格</a>
                    @endif
                </div>
            </article>
        @empty
            <div class="panel">没有找到匹配的商品。</div>
        @endforelse
    </div>

    @if ($products->hasPages())
        <nav class="pager" aria-label="商品分页">
            @if ($products->onFirstPage())
                <span class="pager-link disabled">上一页</span>
            @else
                <a class="pager-link" href="{{ $products->previousPageUrl() }}">上一页</a>
            @endif

            @for ($page = 1; $page <= $products->lastPage(); $page++)
                @if ($page === $products->currentPage())
                    <span class="pager-link active">{{ $page }}</span>
                @else
                    <a class="pager-link" href="{{ $products->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($products->hasMorePages())
                <a class="pager-link" href="{{ $products->nextPageUrl() }}">下一页</a>
            @else
                <span class="pager-link disabled">下一页</span>
            @endif
        </nav>
    @endif
@endsection
