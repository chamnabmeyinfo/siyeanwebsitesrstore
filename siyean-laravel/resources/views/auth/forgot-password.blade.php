@extends('layouts.guest')

@section('title', __('Forgot password').' — '.config('app.name'))

@section('content')
    <h1>{{ __('Forgot password') }}</h1>
    <p class="muted" style="margin-top:0;text-align:left;margin-bottom:1rem;">{{ __('We will email a reset link if an account exists for that address.') }}</p>

    @if (session('status'))
        <p class="success">{{ session('status') }}</p>
    @endif

    <form method="post" action="{{ url('/auth/forgot-password') }}">
        @csrf
        <label for="email">{{ __('Email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <button type="submit">{{ __('Email password reset link') }}</button>
    </form>

    <p class="muted"><a href="{{ route('login') }}">{{ __('Back to sign in') }}</a></p>
@endsection
