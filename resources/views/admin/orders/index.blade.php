@extends('admin.partials.shell')

@section('title', __('ui.admin.order_management'))

@section('content')
    <div class="page-head">
        <h1>{{ __('ui.admin.order_management') }}</h1>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ui.admin.order_no') }}</th>
                    <th>{{ __('ui.admin.customer') }}</th>
                    <th>{{ __('ui.common.product') }}</th>
                    <th>{{ __('ui.admin.amount') }}</th>
                    <th>{{ __('ui.common.status') }}</th>
                    <th>{{ __('ui.admin.created_at') }}</th>
                    <th>{{ __('ui.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_no }}</strong>
                        </td>
                        <td>
                            <div>{{ $order->customer_name }}</div>
                            <div class="muted">{{ $order->customer_phone }}</div>
                            <div class="muted">{{ $order->customer_email }}</div>
                        </td>
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
                            <a class="button secondary" href="{{ route('admin.orders.show', $order) }}">{{ __('ui.common.view') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted">{{ __('ui.admin.no_orders') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $orders->links() }}
@endsection
