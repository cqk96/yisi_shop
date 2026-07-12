@extends('admin.partials.shell')

@section('title', '商品管理')

@section('content')
    <div class="page-head">
        <h1>商品管理</h1>
        <a class="button" href="{{ route('admin.products.create') }}">新增商品</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>商品</th>
                    <th>分类</th>
                    <th>价格</th>
                    <th>SKU 库存</th>
                    <th>图片</th>
                    <th>状态</th>
                    <th>操作</th>
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
                            @foreach ($product->prices as $price)
                                <div>{{ $price->currency_code }} {{ number_format($price->price, 2) }}</div>
                            @endforeach
                        </td>
                        <td>
                            <strong>总库存 {{ $product->stock }}</strong>
                            @foreach ($product->skus->take(4) as $sku)
                                <div class="muted">{{ $sku->name }}：{{ $sku->stock }}</div>
                            @endforeach
                        </td>
                        <td>
                            <div class="thumbs">
                                @foreach ($product->images->take(3) as $image)
                                    <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}">
                                @endforeach
                            </div>
                        </td>
                        <td>{{ $product->is_active ? '上架' : '下架' }}</td>
                        <td>
                            <div class="actions">
                                <a class="button secondary" href="{{ route('admin.products.edit', $product) }}">编辑</a>
                                <a class="button secondary" href="{{ route('admin.products.qr-code', $product) }}" target="_blank">二维码</a>
                                <form method="post" action="{{ route('admin.products.destroy', $product) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="button danger" type="submit" onclick="return confirm('确定删除该商品？')">删除</button>
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
