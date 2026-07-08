<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FindIt') — Lost & Found</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#main">Skip to main content</a>
<div class="site-shell">
    <header class="topnav">
        <div class="topnav-inner">
            <a href="{{ route('home') }}" class="brand">Find<span>It</span></a>
            <nav class="nav-links" aria-label="Primary">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.index') || request()->routeIs('items.show') ? 'active' : '' }}">Browse</a>
                @auth('web')
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('items.mine') }}" class="{{ request()->routeIs('items.mine') || request()->routeIs('items.create') ? 'active' : '' }}">My Items</a>
                    <a href="{{ route('claims.mine') }}" class="{{ request()->routeIs('claims.mine') ? 'active' : '' }}">My Claims</a>
                @endauth
                <a href="{{ route('admin.login') }}">Admin</a>
            </nav>
            <div class="actions-inline">
                @auth('web')
                    <a href="{{ route('items.create') }}" class="btn btn-accent btn-sm">Report</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-ghost btn-sm" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                @endauth
                <button type="button" class="nav-toggle" data-nav-toggle aria-expanded="false" aria-controls="mobile-nav" aria-label="Open menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>
        <nav id="mobile-nav" class="mobile-nav" data-mobile-nav aria-label="Mobile">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('items.index') }}">Browse</a>
            @auth('web')
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('items.mine') }}">My Items</a>
                <a href="{{ route('claims.mine') }}">My Claims</a>
                <a href="{{ route('items.create') }}">Report Item</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
            <a href="{{ route('admin.login') }}">Admin</a>
        </nav>
    </header>

    <main id="main" class="site-main">
        @if(session('success'))
            <div class="container" style="padding-top:1rem;" role="status">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="container" style="padding-top:1rem;" role="alert">
                <div class="alert alert-error">{{ $errors->first() }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-inner">
            <div><strong>FindIt</strong> — campus lost & found</div>
            <div>Oracle · Laravel · PL/SQL</div>
        </div>
    </footer>
</div>
</body>
</html>
