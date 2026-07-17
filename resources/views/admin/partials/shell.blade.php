@extends('admin.layout')

@section('body')
    <div class="shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}">{{ __('ui.admin.brand') }}</a>
            <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">{{ __('ui.admin.dashboard') }}</a>
            <a class="side-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">{{ __('ui.admin.category_management') }}</a>
            <a class="side-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">{{ __('ui.admin.product_management') }}</a>
            <a class="side-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">{{ __('ui.admin.order_management') }}</a>
            <a class="side-link" href="{{ route('shop.index') }}" target="_blank">{{ __('ui.admin.open_shop') }}</a>
        </aside>

        <div class="main">
            <header class="topbar">
                <span>{{ __('ui.admin.current_account', ['email' => auth()->user()->email]) }}</span>
                @include('partials.language-switcher')
                <form method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">{{ __('ui.admin.logout') }}</button>
                </form>
            </header>
            <main class="content">
                @if (session('status'))
                    <div class="alert success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
@endsection
