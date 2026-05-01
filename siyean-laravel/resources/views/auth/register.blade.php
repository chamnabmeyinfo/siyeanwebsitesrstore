@extends('layouts.guest')

@section('title', __('Register').' — '.config('app.name'))

@section('content')
    <h1>{{ __('Create account') }}</h1>
    <p class="muted" style="margin-top:0;text-align:left;margin-bottom:1rem;">{{ __('For customer / general website access. Store staff still use') }} <a href="/login">/login</a>.</p>

    <form method="post" action="{{ url('/auth/register') }}">
        @csrf
        <label for="name">{{ __('Name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
        @error('name')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="email">{{ __('Email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password">{{ __('Password') }}</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password_confirmation">{{ __('Confirm password') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

        <button type="submit">{{ __('Register') }}</button>
    </form>

    <p class="muted"><a href="{{ route('login') }}">{{ __('Already registered?') }}</a></p>
@endsection
