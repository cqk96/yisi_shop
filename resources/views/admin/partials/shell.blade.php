@extends('admin.layout')

@section('body')
    <div class="shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}">LaravelShop 后台</a>
            <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">仪表盘</a>
            <a class="side-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">分类管理</a>
            <a class="side-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">商品管理</a>
            <a class="side-link" href="{{ route('shop.index') }}" target="_blank">打开商城</a>
        </aside>

        <div class="main">
            <header class="topbar">
                <span>当前账号：{{ auth()->user()->email }}</span>
                <form method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">退出登录</button>
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
