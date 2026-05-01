@extends('layouts.guest')

@section('title', __('Reset password').' — '.config('app.name'))

@section('content')
    <h1>{{ __('Reset password') }}</h1>

    <form method="post" action="{{ url('/auth/reset-password') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email">{{ __('Email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus autocomplete="username">
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password">{{ __('New password') }}</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password_confirmation">{{ __('Confirm password') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

        <button type="submit">{{ __('Reset password') }}</button>
    </form>

    <p class="muted"><a href="{{ route('login') }}">{{ __('Back to sign in') }}</a></p>
@endsection
