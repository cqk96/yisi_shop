@extends('layouts.app')

@section('title', __('ui.cart.title') . ' - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ __('ui.cart.title') }}</h1>
            <p class="muted">{{ __('ui.cart.subtitle') }}</p>
        </div>
        <a class="button secondary" href="{{ route('shop.index') }}">{{ __('ui.shop.continue_shopping') }}</a>
    </div>

    @if ($cartSummary['items']->isEmpty())
        <div class="panel">{{ __('ui.cart.empty') }}</div>
    @else
        <table class="table">
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

        <div class="summary">
            <div>
                <strong>{{ __('ui.common.total') }}: {{ $cartSummary['currency'] }} {{ number_format($cartSummary['total'], 2) }}</strong>
                <div class="muted">{{ __('ui.cart.items_count', ['count' => $cartSummary['count']]) }}</div>
            </div>
            <a class="button" href="{{ route('checkout.create') }}">{{ __('ui.cart.checkout') }}</a>
        </div>
    @endif
@endsection
