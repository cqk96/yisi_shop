@extends('layouts.app')

@section('title', __('ui.shop.my_orders'))

@section('content')
    <style>
        .orders-mobile-list {
            display: none;
        }
        .order-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .order-card-head {
            align-items: flex-start;
            border-bottom: 1px solid var(--line);
            display: flex;
            gap: 10px;
            justify-content: space-between;
            padding: 12px;
        }
        .order-card-no {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.3;
            word-break: break-all;
        }
        .order-status-pill {
            border-radius: 999px;
            flex: 0 0 auto;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            padding: 6px 8px;
        }
        .order-status-pending {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        .order-status-paid,
        .order-status-completed {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }
        .order-status-shipped {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }
        .order-status-cancelled {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }
        .order-card-body {
            padding: 12px;
        }
        .order-card-items {
            display: grid;
            gap: 5px;
            margin-bottom: 10px;
        }
        .order-card-item {
            color: var(--ink);
            font-size: 13px;
            line-height: 1.35;
        }
        .order-card-meta {
            border-top: 1px solid var(--line);
            display: grid;
            gap: 8px;
            grid-template-columns: 1fr 1fr;
            padding-top: 10px;
        }
        .order-card-meta span {
            color: var(--muted);
            display: block;
            font-size: 11px;
            margin-bottom: 2px;
        }
        .order-card-meta strong {
            display: block;
            font-size: 13px;
        }
        .order-card-actions {
            display: grid;
            gap: 8px;
            grid-template-columns: 1fr;
            padding: 0 12px 12px;
        }
        .order-card-actions .button {
            min-height: 36px;
            padding: 7px 10px;
            width: 100%;
        }
        .order-card-actions form {
            margin: 0;
        }
        @media (max-width: 760px) {
            .orders-table {
                display: none;
            }
            .orders-mobile-list {
                display: block;
            }
            .orders-page-head {
                gap: 10px;
                margin-bottom: 14px;
            }
            .orders-page-head h1 {
                font-size: 24px;
            }
            .orders-page-head .button {
                min-height: 36px;
                padding: 7px 10px;
            }
            .orders-empty {
                font-size: 14px;
                padding: 16px;
            }
        }
    </style>

    <div class="page-head orders-page-head">
        <div>
            <h1>{{ __('ui.shop.my_orders') }}</h1>
            <p class="muted">{{ __('ui.checkout.orders_count', ['count' => $orders->total()]) }}</p>
        </div>
        <a class="button secondary" href="{{ route('shop.index') }}">{{ __('ui.shop.continue_shopping') }}</a>
    </div>

    @if ($orders->isEmpty())
        <div class="panel orders-empty">{{ __('ui.checkout.empty_orders') }}</div>
    @else
        <table class="table orders-table">
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

        <div class="orders-mobile-list">
            @foreach ($orders as $order)
                <article class="order-card">
                    <div class="order-card-head">
                        <div>
                            <div class="muted">{{ __('ui.checkout.order_no') }}</div>
                            <div class="order-card-no">{{ $order->order_no }}</div>
                        </div>
                        <span class="order-status-pill order-status-{{ $order->status }}">{{ __('ui.status.' . $order->status) }}</span>
                    </div>
                    <div class="order-card-body">
                        <div class="order-card-items">
                            @foreach ($order->items->take(3) as $item)
                                <div class="order-card-item">{{ $item->product_name }} x {{ $item->quantity }}</div>
                            @endforeach
                            @if ($order->items->count() > 3)
                                <div class="muted">{{ __('ui.admin.more_products', ['count' => $order->items->count() - 3]) }}</div>
                            @endif
                        </div>
                        <div class="order-card-meta">
                            <div>
                                <span>{{ __('ui.checkout.order_amount') }}</span>
                                <strong>{{ $order->currency }} {{ number_format($order->total, 2) }}</strong>
                            </div>
                            <div>
                                <span>{{ __('ui.admin.created_at') }}</span>
                                <strong>{{ $order->created_at->format('Y-m-d H:i') }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="order-card-actions">
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
                </article>
            @endforeach
        </div>

        {{ $orders->links() }}
    @endif
@endsection
