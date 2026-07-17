@extends('admin.partials.shell')

@section('title', $category->exists ? __('ui.admin.edit_category') : __('ui.admin.add_category'))

@section('content')
    <div class="page-head">
        <h1>{{ $category->exists ? __('ui.admin.edit_category') : __('ui.admin.add_category') }}</h1>
        <a class="button secondary" href="{{ route('admin.categories.index') }}">{{ __('ui.common.back') }}</a>
    </div>

    <form class="panel" method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($category->exists)
            @method('put')
        @endif

        <div class="form-grid">
            <div>
                <label for="name">{{ __('ui.admin.category_name') }}</label>
                <input id="name" name="name" value="{{ old('name', $category->name) }}" required>
            </div>
            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="{{ __('ui.admin.auto_slug_hint') }}">
            </div>
            <div class="full">
                <label for="description">{{ __('ui.common.description') }}</label>
                <textarea id="description" name="description">{{ old('description', $category->description) }}</textarea>
            </div>
        </div>

        <div style="margin-top: 18px;">
            <button class="button" type="submit">{{ __('ui.admin.save_category') }}</button>
        </div>
    </form>
@endsection
