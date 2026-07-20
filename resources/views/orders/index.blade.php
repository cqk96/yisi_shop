@extends('layouts.app')

@section('title', __('ui.shop.my_orders') . ' - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ __('ui.shop.my_orders') }}</h1>
            <p class="muted">{{ __('ui.checkout.orders_count', ['count' => $orders->total()]) }}</p>
        </div>
        <a class="button secondary" href="{{ route('shop.index') }}">{{ __('ui.shop.continue_shopping') }}</a>
    </div>

    @if ($orders->isEmpty())
        <div class="panel">{{ __('ui.checkout.empty_orders') }}</div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ui.checkout.order_no') }}</th>
                    <th>{{ __('ui.checkout.order_items') }}</th>
                    <th>{{ __('ui.checkout.order_amount') }}</th>
                    <th>{{ __('ui.common.status') }}</th>
                    <th>{{ __('ui.admin.created_at') }}</th>
                    <th>{{ __('ui.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_no }}</td>
                        <td>
                            @foreach ($order->items->take(3) as $item)
                                <div>{{ $item->product_name }} x {{ $item->quantity }}</div>
                            @endforeach
                            @if ($order->items->count() > 3)
                                <div class="muted">{{ __('ui.admin.more_products', ['count' => $order->items->count() - 3]) }}</div>
                            @endif
                        </td>
                        <td>{{ $order->currency }} {{ number_format($order->total, 2) }}</td>
                        <td>{{ __('ui.status.' . $order->status) }}</td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div class="line-actions">
                                <a class="button secondary" href="{{ route('orders.show', $order) }}">{{ __('ui.common.view') }}</a>
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
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $orders->links() }}
    @endif
@endsection
