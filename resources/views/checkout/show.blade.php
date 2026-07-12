@extends('layouts.app')

@section('title', '订单详情 - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>订单提交成功</h1>
            <p class="muted">订单号：{{ $order->order_no }}</p>
        </div>
        <a class="button" href="{{ route('shop.index') }}">继续购物</a>
    </div>

    <div class="panel" style="margin-bottom: 18px;">
        <p><strong>收货人：</strong>{{ $order->customer_name }}</p>
        <p><strong>联系电话：</strong>{{ $order->customer_phone }}</p>
        <p><strong>收货地址：</strong>{{ $order->shipping_address }}</p>
        <p><strong>订单状态：</strong>{{ $order->status }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>商品</th>
                <th>SKU</th>
                <th>单价</th>
                <th>数量</th>
                <th>小计</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>
                        {{ $item->sku_name ?: '-' }}
                        @if ($item->sku_code)
                            <div class="muted">{{ $item->sku_code }}</div>
                        @endif
                    </td>
                    <td>&yen;{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>&yen;{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <strong>订单合计：&yen;{{ number_format($order->total, 2) }}</strong>
    </div>
@endsection
