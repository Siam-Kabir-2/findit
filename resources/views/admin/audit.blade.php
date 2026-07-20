@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('heading', 'Activity timeline')

@section('content')
<div class="stat-grid audit-stats">
    <div class="stat"><strong>{{ $stats['total'] }}</strong><span>Total events</span></div>
    <div class="stat"><strong>{{ $stats['today'] }}</strong><span>Today</span></div>
    <div class="stat"><strong>{{ $stats['items'] }}</strong><span>Item events</span></div>
    <div class="stat"><strong>{{ $stats['claims'] }}</strong><span>Claim events</span></div>
</div>

<div class="panel">
    <div class="panel-title">
        <div>
            <h3>What changed</h3>
            <p class="meta" style="margin:0.25rem 0 0;">Plain-language trail of item and claim changes (written by database triggers).</p>
        </div>
    </div>

    <div class="filter-chips audit-filters" role="group" aria-label="Filter audit events" data-audit-filters>
        <button type="button" class="chip active" data-filter="all">All</button>
        <button type="button" class="chip" data-filter="item">Items</button>
        <button type="button" class="chip" data-filter="claim">Claims</button>
        <button type="button" class="chip" data-filter="INSERT">Created</button>
        <button type="button" class="chip" data-filter="UPDATE">Updated</button>
        <button type="button" class="chip" data-filter="DELETE">Deleted</button>
    </div>

    @if(count($events))
        <div class="audit-timeline" data-audit-list>
            @foreach($events as $event)
                <article
                    class="audit-event tone-{{ $event->tone }}"
                    data-entity="{{ $event->entity }}"
                    data-action="{{ $event->action }}"
                >
                    <div class="audit-event-mark" aria-hidden="true"></div>
                    <div class="audit-event-body">
                        <div class="audit-event-top">
                            <div class="badge-row">
                                <span class="badge badge-{{ $event->entity === 'claim' ? 'claimed' : 'found' }}">
                                    {{ $event->entity === 'claim' ? 'Claim' : 'Item' }}
                                </span>
                                @if($event->item_type)
                                    <span class="badge {{ strtolower($event->item_type) === 'lost' ? 'badge-lost' : 'badge-found' }}">
                                        {{ $event->item_type }}
                                    </span>
                                @endif
                                <span class="badge audit-action-{{ strtolower($event->action) }}">{{ $event->action }}</span>
                            </div>
                            <time class="meta" datetime="{{ $event->when_exact }}" title="{{ $event->when_exact }}">
                                {{ $event->when_label }}
                            </time>
                        </div>

                        <h4 class="audit-event-title">{{ $event->headline }}</h4>
                        <p class="audit-event-detail">{{ $event->detail }}</p>

                        <div class="audit-event-meta">
                            @if($event->status_changed)
                                <div class="audit-status-flow">
                                    <span class="badge badge-{{ strtolower($event->old_status) }}">{{ $event->old_status }}</span>
                                    <span class="audit-arrow" aria-hidden="true">→</span>
                                    <span class="badge badge-{{ strtolower($event->new_status) }}">{{ $event->new_status }}</span>
                                </div>
                            @elseif($event->new_status && $event->action === 'INSERT')
                                <span class="badge badge-{{ strtolower($event->new_status) }}">Started as {{ $event->new_status }}</span>
                            @endif

                            <span class="meta">By {{ $event->actor }}</span>

                            @if($event->item_id)
                                <a href="{{ route('items.show', $event->item_id) }}" class="link-accent">View item</a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <p class="meta audit-empty-filter" data-audit-empty hidden>No events match this filter.</p>
    @else
        <div class="empty-state">
            <h3>No activity yet</h3>
            <p>When items or claims are created, updated, or deleted, those events appear here automatically.</p>
        </div>
    @endif
</div>
@endsection
