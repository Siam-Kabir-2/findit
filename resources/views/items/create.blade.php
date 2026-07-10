@extends('layouts.app')

@section('title', 'Report Item')

@section('content')
<section class="section">
    <div class="container" style="max-width:720px;">
        <div class="crumb">
            <a href="{{ route('items.mine') }}">My items</a>
            <span>/</span>
            <span>Report</span>
        </div>
        <div class="page-header">
            <div>
                <span class="eyebrow">New listing</span>
                <h2>Report an item</h2>
                <p>Stored through Oracle PL/SQL <code>findit_pkg.add_item</code>.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="panel form-grid">
            @csrf
            <div>
                <label for="item_name">Item name</label>
                <input id="item_name" name="item_name" value="{{ old('item_name') }}" required>
            </div>
            <div>
                <label for="item_description">Description</label>
                <textarea id="item_description" name="item_description">{{ old('item_description') }}</textarea>
            </div>
            <div class="form-grid two">
                <div>
                    <label for="item_type">Type</label>
                    <select id="item_type" name="item_type" required>
                        <option value="LOST" @selected(old('item_type')==='LOST')>Lost</option>
                        <option value="FOUND" @selected(old('item_type')==='FOUND')>Found</option>
                    </select>
                </div>
                <div>
                    <label for="lost_or_found_date">Date</label>
                    <input id="lost_or_found_date" type="date" name="lost_or_found_date" value="{{ old('lost_or_found_date', date('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="form-grid two">
                <div>
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" @selected((string)old('category_id')===(string)$category->category_id)>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location_id">Location</label>
                    <select id="location_id" name="location_id" required>
                        @foreach($locations as $location)
                            <option value="{{ $location->location_id }}" @selected((string)old('location_id')===(string)$location->location_id)>{{ $location->location_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="item_image">Photo (optional)</label>
                <input id="item_image" type="file" name="item_image" accept="image/*">
            </div>
            <div class="actions-inline">
                <button class="btn btn-accent" type="submit">Post item</button>
                <a href="{{ route('items.mine') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
