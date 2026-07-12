@extends('admin.layout')

@section('title', '登录')

@section('body')
    <main class="login">
        <form class="login-card" method="post" action="{{ route('admin.login.store') }}">
            @csrf
            <h1 style="margin-bottom: 8px;">后台登录</h1>
            <p class="muted">使用超级管理员账号进入商城后台。</p>

            @if ($errors->any())
                <div class="alert error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <label for="account">账号</label>
            <input id="account" type="text" name="account" value="{{ old('account', 'yisi') }}" required>

            <label for="password" style="margin-top: 14px;">密码</label>
            <input id="password" type="password" name="password" required>

            <label style="align-items: center; display: flex; gap: 8px; font-weight: 400; margin: 14px 0;">
                <input type="checkbox" name="remember" value="1" style="width: auto;"> 记住登录
            </label>

            <button class="button" type="submit" style="width: 100%;">登录</button>
        </form>
    </main>
@endsection
