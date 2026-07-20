@extends('layouts.app')

@section('title', 'Browse Items')

@section('content')
<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <span class="eyebrow">Campus board</span>
                <h2>Browse lost & found</h2>
                <p>Filter by name, type, status, category, or location.</p>
            </div>
            @auth
                <a href="{{ route('items.create') }}" class="btn btn-accent">Report item</a>
            @endauth
        </div>

        <div class="toolbar">
            <div class="filter-chips" role="group" aria-label="Board density">
                <form method="POST" action="{{ route('preferences.board-view') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="board_view" value="comfortable">
                    <button type="submit" class="chip {{ ($boardView ?? 'comfortable') === 'comfortable' ? 'active' : '' }}">Comfortable</button>
                </form>
                <form method="POST" action="{{ route('preferences.board-view') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="board_view" value="compact">
                    <button type="submit" class="chip {{ ($boardView ?? '') === 'compact' ? 'active' : '' }}">Compact</button>
                </form>
            </div>
        </div>

        <div class="filter-chips" role="group" aria-label="Quick type filters">
            <a href="{{ route('items.index') }}" class="chip {{ !request('type') ? 'active' : '' }}">All</a>
            <a href="{{ route('items.index', array_merge(request()->except('page'), ['type' => 'LOST'])) }}" class="chip {{ request('type') === 'LOST' ? 'active' : '' }}">Lost</a>
            <a href="{{ route('items.index', array_merge(request()->except('page'), ['type' => 'FOUND'])) }}" class="chip {{ request('type') === 'FOUND' ? 'active' : '' }}">Found</a>
        </div>

        <form method="GET" action="{{ route('items.index') }}" class="filters">
            <div>
                <label for="q">Search</label>
                <input id="q" name="q" value="{{ request('q') }}" placeholder="Item name">
            </div>
            <div>
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="">All</option>
                    <option value="LOST" @selected(request('type')==='LOST')>Lost</option>
                    <option value="FOUND" @selected(request('type')==='FOUND')>Found</option>
                </select>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All</option>
                    @foreach(['PENDING','FOUND','CLAIMED','RETURNED','REJECTED'] as $st)
                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" @selected((string)request('category_id')===(string)$category->category_id)>{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                @include('partials.location-select', [
                    'locations' => $locations,
                    'selected' => request('location_id'),
                    'label' => 'Location',
                    'emptyLabel' => 'All KUET spots',
                    'allowEmpty' => true,
                    'required' => false,
                    'showPreview' => false,
                ])
            </div>
            <button class="btn btn-primary" type="submit">Apply</button>
        </form>

        @if(request()->hasAny(['q','type','status','category_id','location_id']))
            <div class="toolbar">
                <p class="meta">{{ $items->count() }} result{{ $items->count() === 1 ? '' : 's' }}</p>
                <a href="{{ route('items.index') }}" class="btn btn-ghost btn-sm">Clear filters</a>
            </div>
        @endif

        @if($items->count())
            <div class="item-grid {{ ($boardView ?? 'comfortable') === 'compact' ? 'item-grid-compact' : '' }}">
                @foreach($items as $item)
                    <a href="{{ route('items.show', $item) }}" class="item-tile reveal">
                        <div class="item-media">
                            @if(!empty($item->item_image))
                                <img src="{{ asset('storage/'.$item->item_image) }}" alt="{{ $item->item_name }}" loading="lazy">
                            @endif
                        </div>
                        <div class="item-body">
                            <div class="badge-row">
                                <span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span>
                                <span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span>
                            </div>
                            <h3>{{ $item->item_name }}</h3>
                            <div class="meta">{{ $item->category->category_name }} · {{ $item->location->location_name }}</div>
                            <div class="meta">Posted by {{ $item->user->name }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <h3>No matching items</h3>
                <p>Try clearing filters or report a new item to the board.</p>
                <a href="{{ route('items.index') }}" class="btn btn-ghost btn-sm">Reset browse</a>
            </div>
        @endif
    </div>
</section>
@endsection
