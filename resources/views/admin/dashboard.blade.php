@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('heading', 'Operations overview')

@section('content')
<div class="stat-grid">
    <a href="{{ route('admin.users.index') }}" class="stat clickable"><strong>{{ $stats['total_users'] }}</strong><span>Users</span></a>
    <a href="{{ route('admin.items.index') }}" class="stat clickable"><strong>{{ $stats['total_items'] }}</strong><span>Items</span></a>
    <a href="{{ route('admin.items.index') }}" class="stat clickable"><strong>{{ $stats['lost_items'] }}</strong><span>Lost</span></a>
    <a href="{{ route('admin.items.index') }}" class="stat clickable"><strong>{{ $stats['found_items'] }}</strong><span>Found</span></a>
    <a href="{{ route('admin.claims.index', ['status' => 'PENDING']) }}" class="stat clickable"><strong>{{ $stats['pending_claims'] }}</strong><span>Pending claims</span></a>
    <a href="{{ route('admin.claims.index', ['status' => 'APPROVED']) }}" class="stat clickable"><strong>{{ $stats['approved_claims'] }}</strong><span>Approved claims</span></a>
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
        <h3>Latest audit events</h3>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-ghost btn-sm">Full log</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>Table</th>
                <th>Record</th>
                <th>Action</th>
                <th>Change</th>
                <th>By</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentAudit as $log)
                <tr>
                    <td>{{ $log->table_name }}</td>
                    <td>{{ $log->record_id }}</td>
                    <td>{{ $log->action_type }}</td>
                    <td>{{ $log->old_status ?: '—' }} → {{ $log->new_status ?: '—' }}</td>
                    <td>{{ $log->action_by }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No audit events yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <p class="meta" style="margin-top:0.85rem;">Stats from service queries · audit from MySQL triggers</p>
</div>
@endsection
