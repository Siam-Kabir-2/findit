@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="auth-split">
    <div class="auth-visual">
        <div>
            <div class="brand">Find<span>It</span></div>
            <h1>Join FindIt</h1>
            <p>Create an account to post items and submit verified claims.</p>
        </div>
    </div>
    <div class="auth-form-wrap">
        <form method="POST" action="{{ route('register.submit') }}" class="form-panel" style="max-width:30rem;">
            @csrf
            <h2 class="font-display" style="margin:0 0 1.25rem;">Create account</h2>
            <div class="form-grid">
                <div>
                    <label for="name">Full name</label>
                    <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>
                <div class="form-grid two">
                    <div>
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password">
                    </div>
                    <div>
                        <label for="password_confirmation">Confirm</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                </div>
                <div>
                    <label for="address">Address</label>
                    <input id="address" name="address" value="{{ old('address') }}" autocomplete="street-address">
                </div>
                <button class="btn btn-primary" type="submit">Create account</button>
                <p class="meta">Already registered? <a href="{{ route('login') }}" style="color:var(--accent);font-weight:600;">Login</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
