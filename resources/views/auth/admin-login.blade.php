@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="auth-split">
    <div class="auth-visual">
        <div>
            <div class="brand">Find<span>It</span></div>
            <h1>Admin console</h1>
            <p>Review claims, manage taxonomy, and monitor audit activity.</p>
        </div>
    </div>
    <div class="auth-form-wrap">
        <form method="POST" action="{{ route('admin.login.submit') }}" class="form-panel">
            @csrf
            <h2 class="font-display" style="margin:0 0 0.35rem;">Admin sign in</h2>
            <p class="meta" style="margin-bottom:1.35rem;">Demo: admin.one@university.edu / admin_pass_secure_001</p>
            <div class="form-grid">
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                </div>
                <button class="btn btn-primary" type="submit">Enter console</button>
            </div>
        </form>
    </div>
</div>
@endsection
