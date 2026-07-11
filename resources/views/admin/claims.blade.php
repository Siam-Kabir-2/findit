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
            <th>ID</th>
            <th>Item</th>
            <th>Claimant</th>
            <th>Proof</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($claims as $claim)
            <tr>
                <td>{{ $claim->claim_id }}</td>
                <td>
                    <div style="font-weight:600;"><a href="{{ route('items.show', $claim->item_id) }}">{{ $claim->item_name }}</a></div>
                    <div class="meta">{{ $claim->item_type }} · item {{ $claim->item_status }}</div>
                </td>
                <td>
                    <div>{{ $claim->claimant_name }}</div>
                    <div class="meta">{{ $claim->claimant_email }}</div>
                </td>
                <td>
                    <div>{{ $claim->claim_message ?: '—' }}</div>
                    <div class="meta">{{ $claim->proof_description ?: 'No proof details' }}</div>
                </td>
                <td><span class="badge badge-{{ strtolower($claim->claim_status) }}">{{ $claim->claim_status }}</span></td>
                <td>
                    @if($claim->claim_status === 'PENDING')
                        <div class="actions-inline">
                            <form method="POST" action="{{ route('admin.claims.approve', $claim->claim_id) }}">
                                @csrf
                                <button class="btn btn-accent btn-sm" type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.claims.reject', $claim->claim_id) }}">
                                @csrf
                                <button class="btn btn-danger btn-sm" type="submit">Reject</button>
                            </form>
                        </div>
                    @else
                        <span class="meta">Processed</span>
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
    <p>Try another filter, or wait for users to submit new claim requests.</p>
    <a href="{{ route('admin.claims.index', ['status' => 'ALL']) }}" class="btn btn-ghost btn-sm">View all claims</a>
</div>
@endif
@endsection
