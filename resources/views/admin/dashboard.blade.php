@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('heading', 'Operations overview')

@section('content')
<div class="stat-grid">
    <a href="{{ route('admin.users.index') }}" class="stat clickable stat-users"><strong>{{ $stats['total_users'] }}</strong><span>Users</span></a>
    <a href="{{ route('admin.items.index') }}" class="stat clickable stat-items"><strong>{{ $stats['total_items'] }}</strong><span>Items</span></a>
    <a href="{{ route('admin.items.index') }}" class="stat clickable stat-lost"><strong>{{ $stats['lost_items'] }}</strong><span>Lost</span></a>
    <a href="{{ route('admin.items.index') }}" class="stat clickable stat-found"><strong>{{ $stats['found_items'] }}</strong><span>Found</span></a>
    <a href="{{ route('admin.claims.index', ['status' => 'PENDING']) }}" class="stat clickable stat-pending"><strong>{{ $stats['pending_claims'] }}</strong><span>Pending claims</span></a>
    <a href="{{ route('admin.claims.index', ['status' => 'APPROVED']) }}" class="stat clickable stat-approved"><strong>{{ $stats['approved_claims'] }}</strong><span>Approved claims</span></a>
</div>

<div class="quick-grid">
    <a href="{{ route('admin.claims.index', ['status' => 'PENDING']) }}" class="quick-link">
        <strong>Review claims</strong>
        <span>Approve or reject pending ownership requests</span>
    </a>
    <a href="{{ route('admin.items.index') }}" class="quick-link">
        <strong>Manage items</strong>
        <span>Update status or remove listings</span>
    </a>
    <a href="{{ route('admin.audit.index') }}" class="quick-link">
        <strong>Audit trail</strong>
        <span>Trigger-logged item and claim changes</span>
    </a>
</div>

<div class="split-2">
    <div class="panel">
        <div class="panel-title">
            <h3>Recent claims</h3>
            <a href="{{ route('admin.claims.index', ['status' => 'ALL']) }}" class="btn btn-ghost btn-sm">All</a>
        </div>
        <div class="activity-list">
            @forelse($recentClaims as $claim)
                <div class="activity-item">
                    <div>
                        <strong>{{ $claim->item_name }}</strong>
                        <div class="meta">{{ $claim->claimant_name }}</div>
                    </div>
                    <span class="badge badge-{{ strtolower($claim->claim_status) }}">{{ $claim->claim_status }}</span>
                </div>
            @empty
                <div class="empty-state">
                    <h3>No claims yet</h3>
                    <p>New claim requests will show up here.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="panel">
        <div class="panel-title">
            <h3>Recent items</h3>
            <a href="{{ route('admin.items.index') }}" class="btn btn-ghost btn-sm">All</a>
        </div>
        <div class="activity-list">
            @forelse($recentItems as $item)
                <div class="activity-item">
                    <div>
                        <strong><a href="{{ route('items.show', $item->item_id) }}">{{ $item->item_name }}</a></strong>
                        <div class="meta">{{ $item->item_type }} · {{ $item->user_name }}</div>
                    </div>
                    <span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span>
                </div>
            @empty
                <div class="empty-state">
                    <h3>No items</h3>
                    <p>Posted items will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="panel" style="margin-top:1rem;">
    <div class="panel-title">
        <h3>Latest activity</h3>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-ghost btn-sm">Full timeline</a>
    </div>
    <div class="activity-list">
        @forelse($recentAudit as $event)
            <div class="activity-item">
                <div>
                    <strong>{{ $event->headline }}</strong>
                    <div class="meta">{{ $event->detail }}</div>
                    @if($event->status_changed)
                        <div class="badge-row" style="margin-top:0.35rem;">
                            <span class="badge badge-{{ strtolower($event->old_status) }}">{{ $event->old_status }}</span>
                            <span class="meta">→</span>
                            <span class="badge badge-{{ strtolower($event->new_status) }}">{{ $event->new_status }}</span>
                        </div>
                    @endif
                </div>
                <div style="text-align:right;">
                    <span class="badge badge-{{ $event->entity === 'claim' ? 'claimed' : 'found' }}">{{ $event->entity === 'claim' ? 'Claim' : 'Item' }}</span>
                    <div class="meta" style="margin-top:0.35rem;">{{ $event->when_label }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <h3>No activity yet</h3>
                <p>Item and claim changes will show up here.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
