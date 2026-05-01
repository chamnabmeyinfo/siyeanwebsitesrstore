@extends('layouts.guest')

@section('title', __('Account').' — '.config('app.name'))

@section('content')
    <h1>{{ __('Your account') }}</h1>
    <p style="color:#cbd5e1;margin-bottom:1rem;">{{ __('Signed in as') }} <strong>{{ $user->email }}</strong></p>

    <a class="btn-primary" href="{{ url('/') }}" style="display:block;margin-bottom:0.5rem;">{{ __('Back to shop') }}</a>

    <form method="post" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-secondary" style="background:#334155;">{{ __('Sign out') }}</button>
    </form>

    <p class="muted">{{ __('POS staff tools use') }} <a href="/login">{{ __('staff login') }}</a>.</p>
@endsection
