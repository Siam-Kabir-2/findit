@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<section class="section">
    <div class="container" style="max-width:720px;">
        <div class="page-header">
            <div>
                <span class="eyebrow">Account</span>
                <h2>Profile</h2>
                <p>Manage your account details and password.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="panel form-grid" style="margin-bottom:1.25rem;">
            @csrf
            @method('PATCH')
            <div>
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="{{ $errors->has('name') ? 'is-invalid' : '' }}">
                <x-field-error name="name" />
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                <x-field-error name="email" />
            </div>
            <div>
                <label for="phone">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                <x-field-error name="phone" />
            </div>
            <div>
                <label for="address">Address</label>
                <input id="address" name="address" value="{{ old('address', $user->address) }}">
                <x-field-error name="address" />
            </div>
            <button class="btn btn-primary" type="submit">Save profile</button>
        </form>

        <form method="POST" action="{{ route('password.update') }}" class="panel form-grid" style="margin-bottom:1.25rem;">
            @csrf
            @method('PUT')
            <h3 class="font-display" style="margin:0;">Update password</h3>
            @if ($errors->updatePassword->any())
                <div class="alert alert-error">
                    <div>
                        <ul class="alert-list">
                            @foreach ($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <div>
                <label for="current_password">Current password</label>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="{{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}">
            </div>
            <div>
                <label for="password">New password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="{{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}">
            </div>
            <div>
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>
            <button class="btn btn-ghost" type="submit">Change password</button>
        </form>
    </div>
</section>
@endsection
