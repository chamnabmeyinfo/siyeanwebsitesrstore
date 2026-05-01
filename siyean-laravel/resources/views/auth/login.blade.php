@extends('layouts.guest')

@section('title', __('Sign in').' — '.config('app.name'))

@section('content')
    <h1>{{ __('Sign in') }}</h1>
    <p class="muted" style="margin-top:0;text-align:left;margin-bottom:1rem;">{{ __('Website account (MySQL). Staff POS sign-in stays at') }} <a href="/login">/login</a>.</p>

    @if (session('status'))
        <p class="success">{{ session('status') }}</p>
    @endif

    <form method="post" action="{{ url('/auth/login') }}">
        @csrf
        <label for="email">{{ __('Email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password">{{ __('Password') }}</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">

        <label class="remember">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            {{ __('Remember me') }}
        </label>

        <button type="submit">{{ __('Sign in') }}</button>
    </form>

    <p class="muted">
        <a href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
        @if (Route::has('register'))
            · <a href="{{ route('register') }}">{{ __('Create an account') }}</a>
        @endif
    </p>
@endsection
