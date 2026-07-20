@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<section class="section">
    <div class="container">
        <div class="page-header reveal">
            <div>
                <span class="eyebrow">Personal Workspace</span>
                <h2>Hello, {{ auth()->user()->name }}</h2>
                <p>Manage your reported items and track your claim requests.</p>
            </div>
            <div class="actions-inline">
                <a href="{{ route('items.create') }}" class="btn btn-accent">Report an item</a>
                <a href="{{ route('items.index') }}" class="btn btn-ghost">Browse board</a>
            </div>
        </div>

        <div class="stat-grid reveal" style="grid-template-columns: repeat(2, 1fr);">
            <div class="stat stat-items">
                <strong>{{ $stats->total_items ?? 0 }}</strong>
                <span>My items</span>
            </div>
            <div class="stat stat-users">
                <strong>{{ $stats->total_claims ?? 0 }}</strong>
                <span>My claims</span>
            </div>
            <div class="stat stat-pending">
                <strong>{{ $stats->pending_claims ?? 0 }}</strong>
                <span>Pending claims</span>
            </div>
            <div class="stat stat-approved">
                <strong>{{ $stats->approved_claims ?? 0 }}</strong>
                <span>Approved claims</span>
            </div>
        </div>

        <div class="quick-grid reveal">
            <a href="{{ route('items.mine') }}" class="quick-link">
                <strong>My items</strong>
                <span>Manage what you reported</span>
            </a>
            <a href="{{ route('claims.mine') }}" class="quick-link">
                <strong>My claims</strong>
                <span>Follow verification status</span>
            </a>
            <a href="{{ route('items.create') }}" class="quick-link">
                <strong>New report</strong>
                <span>Post a lost or found item</span>
            </a>
        </div>

        <div class="panel reveal">
            <div class="panel-title">
                <h3 class="font-display">Recent posts</h3>
                <a href="{{ route('items.mine') }}" class="btn btn-ghost btn-sm">View all →</a>
            </div>
            @if(isset($recentItems) && $recentItems->count())
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th style="text-align:right;">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentItems as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('items.show', $item) }}" class="link-accent" style="font-weight:600;">{{ $item->item_name }}</a>
                                </td>
                                <td><span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span></td>
                                <td>{{ $item->category->category_name }}</td>
                                <td style="text-align:right;"><span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <h3>No posts yet</h3>
                    <p>Report a lost or found item to get started and help the community.</p>
                    <a href="{{ route('items.create') }}" class="btn btn-accent btn-sm" style="margin-top:0.5rem;">Report your first item</a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
