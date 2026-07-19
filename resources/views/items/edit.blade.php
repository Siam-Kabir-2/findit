@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
<section class="section">
    <div class="container" style="max-width:720px;">
        <div class="crumb">
            <a href="{{ route('items.mine') }}">My items</a>
            <span>/</span>
            <span>Edit</span>
        </div>
        <div class="page-header">
            <div>
                <span class="eyebrow">Update listing</span>
                <h2>Edit item</h2>
                <p>Change details for {{ $item->item_name }}.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data" class="panel form-grid">
            @csrf
            @method('PUT')
            <div>
                <label for="item_name">Item name</label>
                <input id="item_name" name="item_name" value="{{ old('item_name', $item->item_name) }}" required>
            </div>
            <div>
                <label for="item_description">Description</label>
                <textarea id="item_description" name="item_description">{{ old('item_description', $item->item_description) }}</textarea>
            </div>
            <div class="form-grid two">
                <div>
                    <label for="item_type">Type</label>
                    <select id="item_type" name="item_type" required>
                        <option value="LOST" @selected(old('item_type', $item->item_type)==='LOST')>Lost</option>
                        <option value="FOUND" @selected(old('item_type', $item->item_type)==='FOUND')>Found</option>
                    </select>
                </div>
                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach(['PENDING','FOUND','CLAIMED','RETURNED','REJECTED'] as $st)
                            <option value="{{ $st }}" @selected(old('status', $item->status)===$st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-grid two">
                <div>
                    <label for="lost_or_found_date">Date</label>
                    <input id="lost_or_found_date" type="date" name="lost_or_found_date" value="{{ old('lost_or_found_date', \Illuminate\Support\Str::of($item->lost_or_found_date)->before(' ')) }}" required>
                </div>
                <div>
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" @selected((string)old('category_id', $item->category_id)===(string)$category->category_id)>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="location_id">Location</label>
                <select id="location_id" name="location_id" required>
                    @foreach($locations as $location)
                        <option value="{{ $location->location_id }}" @selected((string)old('location_id', $item->location_id)===(string)$location->location_id)>{{ $location->location_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="item_image">Replace photo (optional)</label>
                <input id="item_image" type="file" name="item_image" accept="image/*">
                @if($item->item_image)
                    <p class="meta" style="margin-top:0.4rem;">Current: {{ $item->item_image }}</p>
                @endif
            </div>
            <div class="actions-inline">
                <button class="btn btn-accent" type="submit">Save changes</button>
                <a href="{{ route('items.mine') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
