@extends('layouts.app')

@section('title', 'FindIt — Campus Lost & Found')

@section('content')
<section class="hero">
    <div class="hero-inner">
        <p class="hero-brand">Find<span>It</span></p>
        <h1>Lost on campus. Found with clarity.</h1>
        <p class="hero-copy">A streamlined platform for reporting, searching, and verifying lost & found items across your campus community.</p>
        <div class="hero-actions">
            <a href="{{ route('items.index') }}" class="btn btn-accent">Browse the board</a>
            @auth
                <a href="{{ route('items.create') }}" class="btn btn-ghost">Report an item</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-ghost">Create account</a>
            @endauth
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <strong>{{ \App\Models\Item::count() }}</strong>
                <span>Items listed</span>
            </div>
            <div class="hero-stat">
                <strong>{{ \App\Models\User::count() }}</strong>
                <span>Active users</span>
            </div>
            <div class="hero-stat">
                <strong>{{ \App\Models\Claim::where('claim_status','APPROVED')->count() }}</strong>
                <span>Reunited</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head centered reveal">
            <span class="eyebrow">How it works</span>
            <h2>Three steps to recovery</h2>
            <p>No clutter. No confusion. Every claim verified by real admins.</p>
        </div>
        <div class="benefits reveal">
            <div class="benefit">
                <span class="benefit-num">01</span>
                <h3>Post the item</h3>
                <p>Report a lost or found item with name, category, location, date, and an optional photo upload.</p>
            </div>
            <div class="benefit">
                <span class="benefit-num">02</span>
                <h3>Search & match</h3>
                <p>Filter the board by type, status, category, or location to find what you're looking for faster.</p>
            </div>
            <div class="benefit">
                <span class="benefit-num">03</span>
                <h3>Verify & claim</h3>
                <p>Submit proof of ownership. Admins review and approve claims through a secure verification workflow.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="section-row reveal">
            <div class="section-head">
                <span class="eyebrow">Live board</span>
                <h2>Recently posted</h2>
                <p>Fresh listings from across the campus network.</p>
            </div>
            <a href="{{ route('items.index') }}" class="btn btn-ghost btn-sm">View all →</a>
        </div>

        @if($recentItems->count())
            <div class="item-grid">
                @foreach($recentItems as $item)
                    <a href="{{ route('items.show', $item) }}" class="item-tile reveal">
                        <div class="item-media">
                            @if(!empty($item->item_image))
                                <img src="{{ asset('storage/'.$item->item_image) }}" alt="{{ $item->item_name }}" loading="lazy">
                            @else
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1.5" style="margin:auto;position:absolute;inset:0;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                            @endif
                        </div>
                        <div class="item-body">
                            <div class="badge-row">
                                <span class="badge {{ strtolower($item->item_type) === 'lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span>
                                <span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span>
                            </div>
                            <h3>{{ $item->item_name }}</h3>
                            <div class="meta">{{ $item->category->category_name }} · {{ $item->location->location_name }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-state reveal">
                <h3>No items yet</h3>
                <p>Be the first to report a lost or found item on campus.</p>
                <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">Report an item</a>
            </div>
        @endif
    </div>
</section>

<section class="cta-band reveal">
    <div class="cta-copy">
        <h2>Ready when something goes missing</h2>
        <p>Join FindIt and keep campus recovery organized, verified, and fast.</p>
    </div>
    @auth
        <a href="{{ route('items.create') }}" class="btn btn-accent">Report an item</a>
    @else
        <a href="{{ route('register') }}" class="btn btn-accent">Get started free</a>
    @endauth
</section>
@endsection
