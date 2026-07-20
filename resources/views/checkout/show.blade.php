@extends('layouts.app')

@section('title', __('ui.admin.order_detail') . ' - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ __('ui.checkout.order_success') }}</h1>
            <p class="muted">{{ __('ui.checkout.order_no') }}: {{ $order->order_no }}</p>
        </div>
        <div class="line-actions">
            <a class="button secondary" href="{{ route('orders.index') }}">{{ __('ui.shop.my_orders') }}</a>
            <a class="button" href="{{ route('shop.index') }}">{{ __('ui.shop.continue_shopping') }}</a>
        </div>
    </div>

    <div class="panel" style="margin-bottom: 18px;">
        <p><strong>{{ __('ui.checkout.customer_name') }}: </strong>{{ $order->customer_name }}</p>
        <p><strong>{{ __('ui.checkout.contact_phone') }}: </strong>{{ $order->customer_phone }}</p>
        <p><strong>{{ __('ui.checkout.shipping_address') }}: </strong>{{ $order->shipping_address }}</p>
        <p><strong>{{ __('ui.checkout.order_status') }}: </strong>{{ __('ui.status.' . $order->status) }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('ui.common.product') }}</th>
                <th>SKU</th>
                <th>{{ __('ui.common.price') }}</th>
                <th>{{ __('ui.common.quantity') }}</th>
                <th>{{ __('ui.common.subtotal') }}</th>
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
                    <td>{{ $order->currency }} {{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $order->currency }} {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <strong>{{ __('ui.checkout.order_total') }}: {{ $order->currency }} {{ number_format($order->total, 2) }}</strong>
        @if (in_array($order->status, ['pending', 'paid'], true))
            <form method="post" action="{{ route('orders.cancel', $order) }}">
                @csrf
                @method('patch')
                <button class="button danger" type="submit" onclick="return confirm('{{ __('ui.checkout.confirm_cancel_order') }}')">
                    {{ __('ui.checkout.cancel_order') }}
                </button>
            </form>
        @endif
    </div>
@endsection
