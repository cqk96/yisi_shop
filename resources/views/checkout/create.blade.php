@extends('layouts.app')

@section('title', '结算 - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>填写收货信息</h1>
            <p class="muted">当前为演示订单，不接入真实支付。</p>
        </div>
        <div class="price">￥{{ number_format($cartSummary['total'], 2) }}</div>
    </div>

    <form class="panel" method="post" action="{{ route('checkout.store') }}">
        @csrf
        <div class="form-grid">
            <div>
                <label for="customer_name">收货人</label>
                <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
            </div>
            <div>
                <label for="customer_phone">手机号</label>
                <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
            </div>
            <div class="full">
                <label for="customer_email">邮箱</label>
                <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" required>
            </div>
            <div class="full">
                <label for="shipping_address">收货地址</label>
                <textarea id="shipping_address" name="shipping_address" required>{{ old('shipping_address') }}</textarea>
            </div>
        </div>

        <div class="summary">
            <div>
                <strong>订单金额：￥{{ number_format($cartSummary['total'], 2) }}</strong>
                <div class="muted">共 {{ $cartSummary['count'] }} 件商品</div>
            </div>
            <button class="button" type="submit">提交订单</button>
        </div>
    </form>
@endsection
