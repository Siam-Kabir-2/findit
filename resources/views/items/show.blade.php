@extends('layouts.app')

@section('title', $item->item_name)

@section('content')
<section class="section">
    <div class="container">
        <div class="crumb">
            <a href="{{ route('items.index') }}">Browse</a>
            <span>/</span>
            <span>{{ $item->item_name }}</span>
        </div>

        <div class="detail-layout">
            <div class="detail-media {{ empty($item->item_image) ? 'is-empty' : '' }}">
                @if(!empty($item->item_image))
                    <img src="{{ asset('storage/'.$item->item_image) }}" alt="{{ $item->item_name }}">
                @else
                    <div class="detail-media-empty">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <span>No photo uploaded</span>
                    </div>
                @endif
            </div>

            <div class="detail-info panel">
                <div class="badge-row">
                    <span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span>
                    <span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span>
                </div>

                <h1 class="page-title">{{ $item->item_name }}</h1>
                <p class="detail-kicker">{{ $item->category->category_name }} · {{ $item->location->location_name }}</p>
                <p class="detail-desc">{{ $item->item_description ?: 'No description provided.' }}</p>

                <dl class="meta-grid detail-meta">
                    <div class="meta-row">
                        <dt>Date</dt>
                        <dd>{{ \Illuminate\Support\Str::of($item->lost_or_found_date)->before(' ') }}</dd>
                    </div>
                    <div class="meta-row">
                        <dt>Posted by</dt>
                        <dd>{{ $item->user->name }}</dd>
                    </div>
                    <div class="meta-row">
                        <dt>Contact</dt>
                        <dd>{{ $item->user->email }}</dd>
                    </div>
                    <div class="meta-row">
                        <dt>Campus spot</dt>
                        <dd>{{ $item->location->location_name }}</dd>
                    </div>
                </dl>

                @can('update', $item)
                    <div class="actions-inline">
                        <a href="{{ route('items.edit', $item) }}" class="btn btn-ghost btn-sm">Edit item</a>
                    </div>
                @endcan

                @auth
                    @if((int) auth()->id() === (int) $item->user_id)
                        <p class="meta detail-note">This is your posting.</p>
                        <a href="{{ route('items.mine') }}" class="btn btn-ghost btn-sm">Manage my items</a>
                    @elseif(in_array($item->status, ['CLAIMED','RETURNED','REJECTED'], true))
                        <p class="meta detail-note">This item is no longer open for claims.</p>
                    @endif
                @else
                    <p class="meta detail-note"><a href="{{ route('login') }}" class="link-accent">Login</a> to submit a claim.</p>
                @endauth
            </div>
        </div>

        <div class="detail-secondary">
            <div class="panel detail-map-panel">
                <div class="panel-title">
                    <div>
                        <h3>Where on campus</h3>
                        <p class="meta" style="margin:0.2rem 0 0;">Pinned to KUET, Khulna · {{ $item->location->location_name }}</p>
                    </div>
                </div>
                @include('partials.location-map', ['location' => $item->location, 'height' => '280px', 'zoom' => 17])
            </div>

            @auth
                @if((int) auth()->id() !== (int) $item->user_id && !in_array($item->status, ['CLAIMED','RETURNED','REJECTED'], true))
                    <div class="panel detail-claim-panel">
                        <div class="panel-title">
                            <div>
                                <h3>Submit a claim</h3>
                                <p class="meta" style="margin:0.2rem 0 0;">Include proof so admins can verify ownership.</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('claims.store', $item) }}" class="form-grid">
                            @csrf
                            <div>
                                <label for="claim_message">Message</label>
                                <textarea id="claim_message" name="claim_message" placeholder="Why is this yours?">{{ old('claim_message') }}</textarea>
                            </div>
                            <div>
                                <label for="proof_description">Proof details</label>
                                <textarea id="proof_description" name="proof_description" placeholder="Unique marks, serial, contents...">{{ old('proof_description') }}</textarea>
                            </div>
                            <button class="btn btn-accent" type="submit">Submit claim</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</section>
@endsection
