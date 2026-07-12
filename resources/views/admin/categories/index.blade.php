@extends('admin.partials.shell')

@section('title', '分类管理')

@section('content')
    <div class="page-head">
        <h1>分类管理</h1>
        <a class="button" href="{{ route('admin.categories.create') }}">新增分类</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>名称</th>
                    <th>Slug</th>
                    <th>商品数</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            <div class="actions">
                                <a class="button secondary" href="{{ route('admin.categories.edit', $category) }}">编辑</a>
                                <form method="post" action="{{ route('admin.categories.destroy', $category) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="button danger" type="submit" onclick="return confirm('确定删除该分类？')">删除</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $categories->links() }}
@endsection
