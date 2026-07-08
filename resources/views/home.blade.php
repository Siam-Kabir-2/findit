@extends('layouts.app')

@section('title', 'FindIt')

@section('content')
<section class="hero">
    <div class="hero-inner">
        <p class="hero-brand">Find<span>It</span></p>
        <h1>Lost on campus. Found with clarity.</h1>
        <p class="hero-copy">A precise board for reporting, searching, and verifying lost & found items across campus.</p>
        <div class="hero-actions">
            <a href="{{ route('items.index') }}" class="btn btn-accent">Browse board</a>
            @auth('web')
                <a href="{{ route('items.create') }}" class="btn btn-ghost">Report item</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-ghost">Create account</a>
            @endauth
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">Process</span>
            <h2>Built for speed and certainty</h2>
            <p>Three steps. No clutter. Every claim verified.</p>
        </div>
        <div class="benefits reveal">
            <div class="benefit">
                <span class="benefit-num">01 — Report</span>
                <h3>Post the item</h3>
                <p>Capture name, category, location, date, and an optional photo.</p>
            </div>
            <div class="benefit">
                <span class="benefit-num">02 — Search</span>
                <h3>Match faster</h3>
                <p>Filter by type, status, category, or place last seen.</p>
            </div>
            <div class="benefit">
                <span class="benefit-num">03 — Claim</span>
                <h3>Verify ownership</h3>
                <p>Submit proof. Admins decide through Oracle PL/SQL.</p>
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
                <p>Fresh listings from the campus network.</p>
            </div>
            <a href="{{ route('items.index') }}" class="btn btn-ghost btn-sm">View all</a>
        </div>

        @if(count($recentItems))
            <div class="item-grid">
                @foreach($recentItems as $item)
                    <a href="{{ route('items.show', $item->item_id) }}" class="item-tile reveal">
                        <div class="item-media">
                            @if(!empty($item->item_image))
                                <img src="{{ asset('storage/'.$item->item_image) }}" alt="{{ $item->item_name }}">
                            @endif
                        </div>
                        <div class="item-body">
                            <div class="badge-row">
                                <span class="badge {{ strtolower($item->item_type) === 'lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span>
                                <span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span>
                            </div>
                            <h3>{{ $item->item_name }}</h3>
                            <div class="meta">{{ $item->category_name }} · {{ $item->location_name }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="panel empty reveal">No items yet. Be the first to report one.</div>
        @endif
    </div>
</section>

<section class="cta-band reveal">
    <div class="cta-copy">
        <h2>Ready when something goes missing.</h2>
        <p>Join FindIt and keep campus recovery organized, verified, and fast.</p>
    </div>
    @auth('web')
        <a href="{{ route('items.create') }}" class="btn btn-accent">Report an item</a>
    @else
        <a href="{{ route('register') }}" class="btn btn-accent">Get started</a>
    @endauth
</section>
@endsection
