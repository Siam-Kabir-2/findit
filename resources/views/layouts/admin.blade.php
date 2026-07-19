<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — FindIt</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#admin-main">Skip to main content</a>
<div class="admin-layout">
    <aside class="admin-side">
        <a href="{{ route('admin.dashboard') }}" class="brand">Find<span>It</span></a>
        <nav class="admin-nav" aria-label="Admin">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.claims.index') }}" class="{{ request()->routeIs('admin.claims.*') ? 'active' : '' }}">Claims</a>
            <a href="{{ route('admin.items.index') }}" class="{{ request()->routeIs('admin.items.*') ? 'active' : '' }}">Items</a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categories</a>
            <a href="{{ route('admin.locations.index') }}" class="{{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">Locations</a>
            <a href="{{ route('admin.audit.index') }}" class="{{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">Audit Logs</a>
            <a href="{{ route('home') }}">View Site</a>
        </nav>
    </aside>
    <div id="admin-main" class="admin-main">
        <div class="admin-top">
            <div>
                <div class="meta" style="margin-bottom:0.25rem;">Admin console</div>
                <h1 class="page-title">@yield('heading', 'Admin')</h1>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-ghost btn-sm" type="submit">Logout</button>
            </form>
        </div>

        @include('partials.flash')

        @yield('content')
    </div>
</div>
</body>
</html>
