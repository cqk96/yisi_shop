@extends('admin.layout')

@section('title', __('ui.admin.login'))

@section('body')
    <main class="login">
        <form class="login-card" method="post" action="{{ route('admin.login.store') }}">
            @csrf
            @include('partials.language-switcher')
            <h1 style="margin: 12px 0 8px;">{{ __('ui.admin.login_title') }}</h1>
            <p class="muted">{{ __('ui.admin.login_hint') }}</p>

            @if ($errors->any())
                <div class="alert error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <label for="account">{{ __('ui.admin.account') }}</label>
            <input id="account" type="text" name="account" value="{{ old('account', 'yisi') }}" required>

            <label for="password" style="margin-top: 14px;">{{ __('ui.admin.password') }}</label>
            <input id="password" type="password" name="password" required>

            <label style="align-items: center; display: flex; gap: 8px; font-weight: 400; margin: 14px 0;">
                <input type="checkbox" name="remember" value="1" style="width: auto;"> {{ __('ui.admin.remember_login') }}
            </label>

            <button class="button" type="submit" style="width: 100%;">{{ __('ui.admin.login') }}</button>
        </form>
    </main>
@endsection
