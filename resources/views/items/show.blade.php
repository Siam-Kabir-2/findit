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
            <div class="detail-media">
                @if(!empty($item->item_image))
                    <img src="{{ asset('storage/'.$item->item_image) }}" alt="{{ $item->item_name }}">
                @endif
            </div>
            <div class="panel">
                <div class="badge-row" style="margin-bottom:0.75rem;">
                    <span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span>
                    <span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span>
                </div>
                <h1 class="page-title" style="margin-bottom:0.35rem;">{{ $item->item_name }}</h1>
                <p class="meta">{{ $item->category_name }} · {{ $item->location_name }}</p>
                <p style="margin:1rem 0;line-height:1.65;color:var(--ink-soft);">{{ $item->item_description ?: 'No description provided.' }}</p>

                <dl class="meta-grid">
                    <div class="meta-row">
                        <dt>Date</dt>
                        <dd>{{ \Illuminate\Support\Str::of($item->lost_or_found_date)->before(' ') }}</dd>
                    </div>
                    <div class="meta-row">
                        <dt>Posted by</dt>
                        <dd>{{ $item->user_name }}</dd>
                    </div>
                    <div class="meta-row">
                        <dt>Contact</dt>
                        <dd>{{ $item->user_email }}</dd>
                    </div>
                </dl>

                @auth('web')
                    @if((int) auth('web')->id() !== (int) $item->user_id && !in_array($item->status, ['CLAIMED','RETURNED','REJECTED'], true))
                        <hr style="border:none;border-top:1px solid var(--line);margin:1.25rem 0;">
                        <h3 class="font-display" style="margin:0 0 0.75rem;">Submit a claim</h3>
                        <p class="meta" style="margin-bottom:0.85rem;">Include proof details so admins can verify ownership via PL/SQL.</p>
                        <form method="POST" action="{{ route('claims.store', $item->item_id) }}" class="form-grid">
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
                    @elseif((int) auth('web')->id() === (int) $item->user_id)
                        <p class="meta" style="margin-top:1rem;">This is your posting.</p>
                        <a href="{{ route('items.mine') }}" class="btn btn-ghost btn-sm" style="margin-top:0.75rem;">Manage my items</a>
                    @else
                        <p class="meta" style="margin-top:1rem;">This item is no longer open for claims.</p>
                    @endif
                @else
                    <p class="meta" style="margin-top:1rem;"><a href="{{ route('login') }}" style="color:var(--accent);font-weight:600;">Login</a> to submit a claim.</p>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection
