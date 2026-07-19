@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-split">
    <div class="auth-visual">
        <div>
            <div class="brand">Find<span>It</span></div>
            <h1>Welcome back</h1>
            <p>Sign in to report items, track claims, and reconnect with what you lost.</p>
        </div>
    </div>
    <div class="auth-form-wrap">
        <form method="POST" action="{{ route('login') }}" class="form-panel">
            @csrf
            <h2 class="font-display" style="margin:0 0 0.35rem;">Sign in</h2>
            <p class="meta" style="margin-bottom:1.35rem;">Use your campus account to continue.</p>
            <div class="form-grid">
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                    <x-field-error name="email" />
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                    <x-field-error name="password" />
                </div>
                <label style="display:flex;align-items:center;gap:0.5rem;font-weight:500;color:var(--ink-soft);">
                    <input type="checkbox" name="remember" style="width:auto;">
                    Remember me
                </label>
                <button class="btn btn-primary" type="submit">Continue</button>
                <p class="meta">
                    <a href="{{ route('password.request') }}" style="color:var(--accent);font-weight:600;">Forgot password?</a>
                    · No account? <a href="{{ route('register') }}" style="color:var(--accent);font-weight:600;">Register</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
