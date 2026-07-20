@extends('admin.partials.shell')

@section('title', __('ui.admin.product_management'))

@section('content')
    <div class="page-head">
        <h1>{{ __('ui.admin.product_management') }}</h1>
        <a class="button" href="{{ route('admin.products.create') }}">{{ __('ui.admin.add_product') }}</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ui.common.product') }}</th>
                    <th>{{ __('ui.common.category') }}</th>
                    <th>{{ __('ui.common.price') }}</th>
                    <th>{{ __('ui.admin.sales') }}</th>
                    <th>{{ __('ui.admin.sku_stock') }}</th>
                    <th>{{ __('ui.common.image') }}</th>
                    <th>{{ __('ui.common.status') }}</th>
                    <th>{{ __('ui.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <div class="muted">{{ $product->slug }}</div>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>
                            @foreach ($product->prices->whereIn('currency_code', ['USD', 'CUP']) as $price)
                                <div>
                                    {{ $price->currency_code }}
                                    @if ($price->hasDiscount())
                                        {{ number_format($price->discount_price, 2) }}
                                        <span class="muted" style="text-decoration: line-through;">{{ number_format($price->price, 2) }}</span>
                                    @else
                                        {{ number_format($price->price, 2) }}
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td>{{ number_format($product->sales_count) }}</td>
                        <td>
                            <strong>{{ __('ui.admin.total_stock') }} {{ $product->stock }}</strong>
                            @foreach ($product->skus->take(4) as $sku)
                                <div class="muted">{{ $sku->name }}: {{ $sku->stock }}</div>
                            @endforeach
                        </td>
                        <td>
                            <div class="thumbs">
                                @foreach ($product->images->take(3) as $image)
                                    <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}">
                                @endforeach
                            </div>
                        </td>
                        <td>{{ $product->is_active ? __('ui.admin.active') : __('ui.admin.inactive') }}</td>
                        <td>
                            <div class="actions">
                                <a class="button secondary" href="{{ route('admin.products.edit', $product) }}">{{ __('ui.common.edit') }}</a>
                                <a class="button secondary" href="{{ route('admin.products.qr-code', $product) }}" target="_blank">{{ __('ui.admin.qr_code') }}</a>
                                <form method="post" action="{{ route('admin.products.destroy', $product) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="button danger" type="submit" onclick="return confirm('{{ __('ui.common.confirm_delete_product') }}')">{{ __('ui.common.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
@endsection
