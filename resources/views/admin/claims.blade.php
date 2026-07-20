@extends('layouts.admin')

@section('title', 'Claims')
@section('heading', 'Claim verification')

@section('content')
<div class="toolbar">
    <div class="filter-chips">
        <a href="{{ route('admin.claims.index', ['status' => 'PENDING']) }}" class="chip {{ $status === 'PENDING' ? 'active' : '' }}">
            Pending <span class="count">{{ (int) ($counts->total_pending ?? 0) }}</span>
        </a>
        <a href="{{ route('admin.claims.index', ['status' => 'APPROVED']) }}" class="chip {{ $status === 'APPROVED' ? 'active' : '' }}">
            Approved <span class="count">{{ (int) ($counts->total_approved ?? 0) }}</span>
        </a>
        <a href="{{ route('admin.claims.index', ['status' => 'REJECTED']) }}" class="chip {{ $status === 'REJECTED' ? 'active' : '' }}">
            Rejected <span class="count">{{ (int) ($counts->total_rejected ?? 0) }}</span>
        </a>
        <a href="{{ route('admin.claims.index', ['status' => 'ALL']) }}" class="chip {{ $status === 'ALL' ? 'active' : '' }}">
            All <span class="count">{{ (int) ($counts->total_all ?? 0) }}</span>
        </a>
    </div>
</div>

@if(count($claims))
<div class="panel table-wrap">
    <table class="data">
        <thead>
        <tr>
            <th style="width: 70px;">ID</th>
            <th>Item</th>
            <th>Claimant</th>
            <th>Proof</th>
            <th>Status</th>
            <th style="text-align: right;">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($claims as $claim)
            <tr>
                <td class="meta">#{{ $claim->claim_id }}</td>
                <td>
                    <div style="font-weight:600;"><a href="{{ route('items.show', $claim->item_id) }}" class="link-accent">{{ $claim->item_name }}</a></div>
                    <div class="meta" style="margin-top:0.25rem;">
                        <span class="badge {{ strtolower($claim->item_type) === 'lost' ? 'badge-lost' : 'badge-found' }}" style="padding:0.1rem 0.4rem;font-size:0.6rem;">{{ $claim->item_type }}</span>
                        · Item is {{ strtolower($claim->item_status) }}
                    </div>
                </td>
                <td>
                    <div style="font-weight:500;">{{ $claim->claimant_name }}</div>
                    <div class="meta" style="margin-top:0.15rem;"><a href="mailto:{{ $claim->claimant_email }}" class="link-accent">{{ $claim->claimant_email }}</a></div>
                </td>
                <td>
                    <div style="color:var(--ink);">{{ $claim->claim_message ?: '—' }}</div>
                    <div class="meta" style="margin-top:0.25rem;">{{ $claim->proof_description ?: 'No proof details' }}</div>
                </td>
                <td><span class="badge badge-{{ strtolower($claim->claim_status) }}">{{ $claim->claim_status }}</span></td>
                <td style="text-align: right;">
                    @if($claim->claim_status === 'PENDING')
                        <div class="actions-inline" style="justify-content: flex-end;">
                            <form method="POST" action="{{ route('admin.claims.approve', $claim->claim_id) }}" onsubmit="return confirm('Approve this claim? This will transfer ownership and close the item.')">
                                @csrf
                                <button class="btn btn-accent btn-sm" type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.claims.reject', $claim->claim_id) }}" onsubmit="return confirm('Reject this claim?')">
                                @csrf
                                <button class="btn btn-danger btn-sm" type="submit">Reject</button>
                            </form>
                        </div>
                    @else
                        <span class="meta" style="display:inline-flex;align-items:center;gap:0.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Processed
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@else
<div class="empty-state">
    <h3>No {{ strtolower($status === 'ALL' ? '' : $status) }} claims</h3>
    <p>Try another filter, or wait for users to submit new claim requests for verification.</p>
    <a href="{{ route('admin.claims.index', ['status' => 'ALL']) }}" class="btn btn-ghost btn-sm">View all claims</a>
</div>
@endif
@endsection
