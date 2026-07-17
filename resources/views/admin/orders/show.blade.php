@extends('admin.partials.shell')

@section('title', __('ui.admin.order_detail'))

@section('content')
    <div class="page-head">
        <h1>{{ __('ui.admin.order_detail') }}</h1>
        <a class="button secondary" href="{{ route('admin.orders.index') }}">{{ __('ui.common.back') }}</a>
    </div>

    <div class="panel" style="margin-bottom: 18px;">
        <div class="form-grid">
            <div>
                <label>{{ __('ui.admin.order_no') }}</label>
                <input value="{{ $order->order_no }}" disabled>
            </div>
            <div>
                <label>{{ __('ui.admin.created_at') }}</label>
                <input value="{{ $order->created_at->format('Y-m-d H:i:s') }}" disabled>
            </div>
            <div>
                <label>{{ __('ui.admin.customer_name') }}</label>
                <input value="{{ $order->customer_name }}" disabled>
            </div>
            <div>
                <label>{{ __('ui.admin.contact_phone') }}</label>
                <input value="{{ $order->customer_phone }}" disabled>
            </div>
            <div class="full">
                <label>{{ __('ui.common.email') }}</label>
                <input value="{{ $order->customer_email }}" disabled>
            </div>
            <div class="full">
                <label>{{ __('ui.admin.shipping_address') }}</label>
                <textarea disabled>{{ $order->shipping_address }}</textarea>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom: 18px;">
        <form method="post" action="{{ route('admin.orders.update', $order) }}">
            @csrf
            @method('put')
            <div class="form-grid">
                <div>
                    <label for="status">{{ __('ui.checkout.order_status') }}</label>
                    <select id="status" name="status">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ __('ui.status.' . $value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="align-self: end;">
                    <button class="button" type="submit">{{ __('ui.admin.update_status') }}</button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-wrap">
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
    </div>

    <div class="panel" style="margin-top: 18px; text-align: right;">
        <strong>{{ __('ui.admin.order_total') }}: {{ $order->currency }} {{ number_format($order->total, 2) }}</strong>
    </div>
@endsection
