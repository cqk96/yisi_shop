@extends('admin.partials.shell')

@section('title', $category->exists ? '编辑分类' : '新增分类')

@section('content')
    <div class="page-head">
        <h1>{{ $category->exists ? '编辑分类' : '新增分类' }}</h1>
        <a class="button secondary" href="{{ route('admin.categories.index') }}">返回</a>
    </div>

    <form class="panel" method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($category->exists)
            @method('put')
        @endif

        <div class="form-grid">
            <div>
                <label for="name">分类名称</label>
                <input id="name" name="name" value="{{ old('name', $category->name) }}" required>
            </div>
            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="留空自动生成">
            </div>
            <div class="full">
                <label for="description">描述</label>
                <textarea id="description" name="description">{{ old('description', $category->description) }}</textarea>
            </div>
        </div>

        <div style="margin-top: 18px;">
            <button class="button" type="submit">保存分类</button>
        </div>
    </form>
@endsection
