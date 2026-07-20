@extends('layouts.app')

@section('title', 'My Claims')

@section('content')
<section class="section">
    <div class="container">
        <div class="page-header reveal">
            <div>
                <span class="eyebrow">Verification</span>
                <h2>My claims</h2>
                <p>Track claim requests processed by admins.</p>
            </div>
            <a href="{{ route('items.index') }}" class="btn btn-ghost">Browse board</a>
        </div>

        @if($claims->count())
            <div class="panel table-wrap reveal">
                <table class="data">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Claim status</th>
                        <th>Item status</th>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($claims as $claim)
                        <tr>
                            <td><a href="{{ route('items.show', $claim->item_id) }}" class="link-accent" style="font-weight:600;">{{ $claim->item->item_name }}</a></td>
                            <td><span class="badge {{ strtolower($claim->item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $claim->item->item_type }}</span></td>
                            <td><span class="badge badge-{{ strtolower($claim->claim_status) }}">{{ $claim->claim_status }}</span></td>
                            <td><span class="badge badge-{{ strtolower($claim->item->status) }}">{{ $claim->item->status }}</span></td>
                            <td style="color:var(--ink-soft);">{{ $claim->claim_message ?: '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state reveal">
                <h3>No claims yet</h3>
                <p>Find an item on the board and submit proof to start a claim.</p>
                <a href="{{ route('items.index') }}" class="btn btn-accent btn-sm" style="margin-top:0.5rem;">Browse items</a>
            </div>
        @endif
    </div>
</section>
@endsection
