@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <span class="eyebrow">Workspace</span>
                <h2>Hello, {{ auth()->user()->name }}</h2>
                <p>Track your posts and claim requests in one place.</p>
            </div>
            <div class="actions-inline">
                <a href="{{ route('items.create') }}" class="btn btn-accent">Report item</a>
                <a href="{{ route('items.index') }}" class="btn btn-ghost">Browse board</a>
            </div>
        </div>

        <div class="stat-grid" style="grid-template-columns: repeat(2, 1fr);">
            <div class="stat"><strong>{{ $stats->total_items ?? 0 }}</strong><span>My items</span></div>
            <div class="stat"><strong>{{ $stats->total_claims ?? 0 }}</strong><span>My claims</span></div>
            <div class="stat"><strong>{{ $stats->pending_claims ?? 0 }}</strong><span>Pending claims</span></div>
            <div class="stat"><strong>{{ $stats->approved_claims ?? 0 }}</strong><span>Approved claims</span></div>
        </div>

        <div class="quick-grid">
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

        <div class="panel">
            <div class="panel-title">
                <h3>Recent posts</h3>
                <a href="{{ route('items.mine') }}" class="btn btn-ghost btn-sm">View all</a>
            </div>
            @if($recentItems->count())
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentItems as $item)
                            <tr>
                                <td><a href="{{ route('items.show', $item) }}">{{ $item->item_name }}</a></td>
                                <td>{{ $item->item_type }}</td>
                                <td>{{ $item->category->category_name }}</td>
                                <td><span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <h3>No posts yet</h3>
                    <p>Report a lost or found item to get started.</p>
                    <a href="{{ route('items.create') }}" class="btn btn-accent btn-sm">Report item</a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
