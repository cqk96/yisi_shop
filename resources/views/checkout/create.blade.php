@extends('layouts.app')

@section('title', __('ui.checkout.title') . ' - LaravelShop')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ __('ui.checkout.shipping_title') }}</h1>
            <p class="muted">{{ __('ui.checkout.demo_notice') }}</p>
        </div>
        <div class="price">{{ $cartSummary['currency'] }} {{ number_format($cartSummary['total'], 2) }}</div>
    </div>

    <form class="panel" method="post" action="{{ route('checkout.store') }}">
        @csrf
        <div class="form-grid">
            <div>
                <label for="customer_name">{{ __('ui.checkout.customer_name') }}</label>
                <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
            </div>
            <div>
                <label for="customer_phone">{{ __('ui.checkout.customer_phone') }}</label>
                <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
            </div>
            <div class="full">
                <label for="customer_email">{{ __('ui.common.email') }}</label>
                <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" required>
            </div>
            <div class="full">
                <label for="shipping_address">{{ __('ui.checkout.shipping_address') }}</label>
                <textarea id="shipping_address" name="shipping_address" required>{{ old('shipping_address') }}</textarea>
            </div>
        </div>

        <div class="summary">
            <div>
                <strong>{{ __('ui.checkout.order_amount') }}: {{ $cartSummary['currency'] }} {{ number_format($cartSummary['total'], 2) }}</strong>
                <div class="muted">{{ __('ui.cart.items_count', ['count' => $cartSummary['count']]) }}</div>
            </div>
            <button class="button" type="submit">{{ __('ui.checkout.submit_order') }}</button>
        </div>
    </form>
@endsection
