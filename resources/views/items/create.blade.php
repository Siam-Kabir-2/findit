@extends('layouts.app')

@section('title', 'Report Item')

@section('content')
<section class="section">
    <div class="container" style="max-width:720px;">
        <div class="crumb reveal">
            <a href="{{ route('items.mine') }}">My items</a>
            <span>/</span>
            <span>Report</span>
        </div>
        <div class="page-header reveal">
            <div>
                <span class="eyebrow">New listing</span>
                <h2>Report an item</h2>
                <p>Add details and an optional photo so others can match it quickly.</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="panel form-grid reveal">
            @csrf
            
            <div style="margin-bottom: 0.5rem;">
                <h3 class="font-display" style="margin:0 0 0.25rem; font-size:1.15rem; color:var(--primary);">Item Details</h3>
                <p class="meta" style="margin:0;">Provide clear and accurate information.</p>
            </div>

            <div>
                <label for="item_name">Item name</label>
                <input id="item_name" name="item_name" value="{{ old('item_name') }}" required placeholder="e.g. Blue Hydroflask, Dell Laptop">
            </div>
            <div>
                <label for="item_description">Description</label>
                <textarea id="item_description" name="item_description" placeholder="Include any distinguishing features, colors, or marks...">{{ old('item_description') }}</textarea>
            </div>
            
            <div class="form-grid two" style="margin-top: 0.5rem;">
                <div>
                    <label for="item_type">Type</label>
                    <select id="item_type" name="item_type" required>
                        <option value="LOST" @selected(old('item_type')==='LOST')>Lost (I lost something)</option>
                        <option value="FOUND" @selected(old('item_type')==='FOUND')>Found (I found something)</option>
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
                        <option value="" disabled selected>Select category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" @selected((string)old('category_id')===(string)$category->category_id)>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @include('partials.location-select', [
                'locations' => $locations,
                'selected' => old('location_id'),
                'label' => 'KUET location',
                'emptyLabel' => 'Choose campus spot...',
                'showPreview' => true,
            ])
            
            <div style="margin-top: 0.75rem;">
                <label for="item_image">Photo <span class="meta">(optional but recommended)</span></label>
                <div style="position:relative;">
                    <input id="item_image" type="file" name="item_image" accept="image/*" style="padding:0.5rem; background:var(--paper-2); cursor:pointer;">
                </div>
            </div>
            
            <hr class="divider">
            
            <div class="actions-inline" style="justify-content: flex-end;">
                <a href="{{ route('items.mine') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary" type="submit">Post item</button>
            </div>
        </form>
    </div>
</section>
@endsection
