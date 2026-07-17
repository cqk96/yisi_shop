@extends('admin.partials.shell')

@section('title', __('ui.admin.category_management'))

@section('content')
    <div class="page-head">
        <h1>{{ __('ui.admin.category_management') }}</h1>
        <a class="button" href="{{ route('admin.categories.create') }}">{{ __('ui.admin.add_category') }}</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ui.common.name') }}</th>
                    <th>Slug</th>
                    <th>{{ __('ui.common.products') }}</th>
                    <th>{{ __('ui.common.actions') }}</th>
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
                                <a class="button secondary" href="{{ route('admin.categories.edit', $category) }}">{{ __('ui.common.edit') }}</a>
                                <form method="post" action="{{ route('admin.categories.destroy', $category) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="button danger" type="submit" onclick="return confirm('{{ __('ui.common.confirm_delete_category') }}')">{{ __('ui.common.delete') }}</button>
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
