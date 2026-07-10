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
            @auth('web')
                <a href="{{ route('items.create') }}" class="btn btn-accent">Report item</a>
            @endauth
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
                <label for="location_id">Location</label>
                <select id="location_id" name="location_id">
                    <option value="">All</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->location_id }}" @selected((string)request('location_id')===(string)$location->location_id)>{{ $location->location_name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Apply</button>
        </form>

        @if(request()->hasAny(['q','type','status','category_id','location_id']))
            <div class="toolbar">
                <p class="meta">{{ count($items) }} result{{ count($items) === 1 ? '' : 's' }}</p>
                <a href="{{ route('items.index') }}" class="btn btn-ghost btn-sm">Clear filters</a>
            </div>
        @endif

        @if(count($items))
            <div class="item-grid">
                @foreach($items as $item)
                    <a href="{{ route('items.show', $item->item_id) }}" class="item-tile reveal">
                        <div class="item-media">
                            @if(!empty($item->item_image))
                                <img src="{{ asset('storage/'.$item->item_image) }}" alt="{{ $item->item_name }}">
                            @endif
                        </div>
                        <div class="item-body">
                            <div class="badge-row">
                                <span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span>
                                <span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span>
                            </div>
                            <h3>{{ $item->item_name }}</h3>
                            <div class="meta">{{ $item->category_name }} · {{ $item->location_name }}</div>
                            <div class="meta">Posted by {{ $item->user_name }}</div>
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
