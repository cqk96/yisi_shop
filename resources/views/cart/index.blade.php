@extends('layouts.app')

@section('title', '购物车 - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>购物车</h1>
            <p class="muted">同一商品的不同 SKU 会独立计算库存和数量。</p>
        </div>
        <a class="button secondary" href="{{ route('shop.index') }}">继续购物</a>
    </div>

    @if ($cartSummary['items']->isEmpty())
        <div class="panel">购物车还是空的。</div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>商品</th>
                    <th>SKU</th>
                    <th>单价</th>
                    <th>数量</th>
                    <th>小计</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cartSummary['items'] as $item)
                    <tr>
                        <td>
                            <strong>{{ $item['product']->name }}</strong>
                            <div class="muted">{{ $item['product']->category->name ?? '' }}</div>
                        </td>
                        <td>
                            {{ $item['sku']->name }}
                            @if ($item['sku']->code)
                                <div class="muted">{{ $item['sku']->code }}</div>
                            @endif
                            <div class="muted">库存 {{ $item['sku']->stock }}</div>
                        </td>
                        <td>&yen;{{ number_format($item['unit_price'], 2) }}</td>
                        <td>
                            <form class="line-actions" method="post" action="{{ route('cart.items.update', $item['sku']) }}">
                                @csrf
                                @method('patch')
                                <input type="number" name="quantity" min="1" max="{{ max(1, $item['sku']->stock) }}" value="{{ $item['quantity'] }}">
                                <button class="button secondary" type="submit">更新</button>
                            </form>
                        </td>
                        <td>&yen;{{ number_format($item['subtotal'], 2) }}</td>
                        <td>
                            <form method="post" action="{{ route('cart.items.destroy', $item['sku']) }}">
                                @csrf
                                @method('delete')
                                <button class="button danger" type="submit">移除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div>
                <strong>合计：&yen;{{ number_format($cartSummary['total'], 2) }}</strong>
                <div class="muted">共 {{ $cartSummary['count'] }} 件商品</div>
            </div>
            <a class="button" href="{{ route('checkout.create') }}">去结算</a>
        </div>
    @endif
@endsection
