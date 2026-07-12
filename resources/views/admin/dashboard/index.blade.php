@extends('admin.partials.shell')

@section('title', '仪表盘')

@section('content')
    <div class="page-head">
        <h1>仪表盘</h1>
        <a class="button" href="{{ route('admin.products.create') }}">新增商品</a>
    </div>

    <div class="metrics">
        <div class="metric"><span class="muted">分类</span><strong>{{ $categoryCount }}</strong></div>
        <div class="metric"><span class="muted">商品</span><strong>{{ $productCount }}</strong></div>
        <div class="metric"><span class="muted">订单</span><strong>{{ $orderCount }}</strong></div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>最新商品</th>
                    <th>分类</th>
                    <th>库存</th>
                    <th>状态</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($latestProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->is_active ? '上架' : '下架' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
