@extends('layouts.app')

@section('title', __('ui.cart.title'))

@section('content')
    <style>
        .cart-mobile-list {
            display: none;
        }
        .cart-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .cart-card-head {
            border-bottom: 1px solid var(--line);
            padding: 12px;
        }
        .cart-card-title {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 4px;
        }
        .cart-card-body {
            display: grid;
            gap: 10px;
            padding: 12px;
        }
        .cart-card-sku {
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 8px;
        }
        .cart-card-sku strong {
            display: block;
            font-size: 13px;
            line-height: 1.3;
        }
        .cart-card-meta {
            display: grid;
            gap: 8px;
            grid-template-columns: 1fr 1fr;
        }
        .cart-card-meta span {
            color: var(--muted);
            display: block;
            font-size: 11px;
            margin-bottom: 2px;
        }
        .cart-card-meta strong {
            display: block;
            font-size: 13px;
        }
        .cart-card-actions {
            border-top: 1px solid var(--line);
            display: grid;
            gap: 8px;
            padding: 12px;
        }
        .cart-card-update {
            display: grid;
            gap: 8px;
            grid-template-columns: minmax(0, 1fr) auto;
        }
        .cart-card-update input {
            min-height: 36px;
            padding: 7px 9px;
        }
        .cart-card-actions .button {
            min-height: 36px;
            padding: 7px 10px;
            width: 100%;
        }
        .cart-card-actions form {
            margin: 0;
        }
        @media (max-width: 760px) {
            .cart-table {
                display: none;
            }
            .cart-mobile-list {
                display: block;
            }
            .cart-page-head {
                gap: 10px;
                margin-bottom: 14px;
            }
            .cart-page-head h1 {
                font-size: 24px;
            }
            .cart-page-head .button {
                min-height: 36px;
                padding: 7px 10px;
            }
            .cart-empty {
                font-size: 14px;
                padding: 16px;
            }
            .cart-summary {
                align-items: stretch;
                gap: 12px;
            }
            .cart-summary .button {
                min-height: 38px;
                width: 100%;
            }
        }
    </style>

    <div class="page-head cart-page-head">
        <div>
            <h1>{{ __('ui.cart.title') }}</h1>
            <p class="muted">{{ __('ui.cart.subtitle') }}</p>
        </div>
        <a class="button secondary" href="{{ route('shop.index') }}">{{ __('ui.shop.continue_shopping') }}</a>
    </div>

    @if ($cartSummary['items']->isEmpty())
        <div class="panel cart-empty">{{ __('ui.cart.empty') }}</div>
    @else
        <table class="table cart-table">
            <thead>
                <tr>
                    <th>{{ __('ui.common.product') }}</th>
                    <th>SKU</th>
                    <th>{{ __('ui.common.price') }}</th>
                    <th>{{ __('ui.common.quantity') }}</th>
                    <th>{{ __('ui.common.subtotal') }}</th>
                    <th>{{ __('ui.common.actions') }}</th>
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
                            <div class="muted">{{ __('ui.common.stock') }} {{ $item['sku']->stock }}</div>
                        </td>
                        <td>{{ $item['currency'] }} {{ number_format($item['unit_price'], 2) }}</td>
                        <td>
                            <form class="line-actions" method="post" action="{{ route('cart.items.update', $item['sku']) }}">
                                @csrf
                                @method('patch')
                                <input type="number" name="quantity" min="1" max="{{ max(1, $item['sku']->stock) }}" value="{{ $item['quantity'] }}">
                                <button class="button secondary" type="submit">{{ __('ui.common.update') }}</button>
                            </form>
                        </td>
                        <td>{{ $item['currency'] }} {{ number_format($item['subtotal'], 2) }}</td>
                        <td>
                            <form method="post" action="{{ route('cart.items.destroy', $item['sku']) }}">
                                @csrf
                                @method('delete')
                                <button class="button danger" type="submit">{{ __('ui.common.remove') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cart-mobile-list">
            @foreach ($cartSummary['items'] as $item)
                <article class="cart-card">
                    <div class="cart-card-head">
                        <div class="cart-card-title">{{ $item['product']->name }}</div>
                        <div class="muted">{{ $item['product']->category->name ?? '' }}</div>
                    </div>
                    <div class="cart-card-body">
                        <div class="cart-card-sku">
                            <span class="muted">SKU</span>
                            <strong>{{ $item['sku']->name }}</strong>
                            @if ($item['sku']->code)
                                <div class="muted">{{ $item['sku']->code }}</div>
                            @endif
                            <div class="muted">{{ __('ui.common.stock') }} {{ $item['sku']->stock }}</div>
                        </div>
                        <div class="cart-card-meta">
                            <div>
                                <span>{{ __('ui.common.price') }}</span>
                                <strong>{{ $item['currency'] }} {{ number_format($item['unit_price'], 2) }}</strong>
                            </div>
                            <div>
                                <span>{{ __('ui.common.subtotal') }}</span>
                                <strong>{{ $item['currency'] }} {{ number_format($item['subtotal'], 2) }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="cart-card-actions">
                        <form class="cart-card-update" method="post" action="{{ route('cart.items.update', $item['sku']) }}">
                            @csrf
                            @method('patch')
                            <input type="number" name="quantity" min="1" max="{{ max(1, $item['sku']->stock) }}" value="{{ $item['quantity'] }}" aria-label="{{ __('ui.common.quantity') }}">
                            <button class="button secondary" type="submit">{{ __('ui.common.update') }}</button>
                        </form>
                        <form method="post" action="{{ route('cart.items.destroy', $item['sku']) }}">
                            @csrf
                            @method('delete')
                            <button class="button danger" type="submit">{{ __('ui.common.remove') }}</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="summary cart-summary">
            <div>
                <strong>{{ __('ui.common.total') }}: {{ $cartSummary['currency'] }} {{ number_format($cartSummary['total'], 2) }}</strong>
                <div class="muted">{{ __('ui.cart.items_count', ['count' => $cartSummary['count']]) }}</div>
            </div>
            <a class="button" href="{{ route('checkout.create') }}">{{ __('ui.cart.checkout') }}</a>
        </div>
    @endif
@endsection
