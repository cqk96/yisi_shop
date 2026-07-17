@extends('admin.partials.shell')

@section('title', __('ui.admin.dashboard'))

@section('content')
    <div class="page-head">
        <h1>{{ __('ui.admin.dashboard') }}</h1>
        <a class="button" href="{{ route('admin.products.create') }}">{{ __('ui.admin.add_product') }}</a>
    </div>

    <div class="metrics">
        <div class="metric"><span class="muted">{{ __('ui.common.category') }}</span><strong>{{ $categoryCount }}</strong></div>
        <div class="metric"><span class="muted">{{ __('ui.common.product') }}</span><strong>{{ $productCount }}</strong></div>
        <div class="metric"><span class="muted">{{ __('ui.admin.order_management') }}</span><strong>{{ $orderCount }}</strong></div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ui.admin.latest_products') }}</th>
                    <th>{{ __('ui.common.category') }}</th>
                    <th>{{ __('ui.common.stock') }}</th>
                    <th>{{ __('ui.common.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($latestProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->is_active ? __('ui.admin.active') : __('ui.admin.inactive') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
