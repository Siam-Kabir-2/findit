@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
<section class="section">
    <div class="container" style="max-width:720px;">
        <div class="crumb reveal">
            <a href="{{ route('items.mine') }}">My items</a>
            <span>/</span>
            <span>Edit</span>
        </div>
        <div class="page-header reveal">
            <div>
                <span class="eyebrow">Update listing</span>
                <h2>Edit item</h2>
                <p>Change details for <strong style="color:var(--ink);">{{ $item->item_name }}</strong>.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data" class="panel form-grid reveal">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 0.5rem;">
                <h3 class="font-display" style="margin:0 0 0.25rem; font-size:1.15rem; color:var(--primary);">Update Details</h3>
                <p class="meta" style="margin:0;">Modify any incorrect information.</p>
            </div>

            <div>
                <label for="item_name">Item name</label>
                <input id="item_name" name="item_name" value="{{ old('item_name', $item->item_name) }}" required>
            </div>
            <div>
                <label for="item_description">Description</label>
                <textarea id="item_description" name="item_description">{{ old('item_description', $item->item_description) }}</textarea>
            </div>
            
            <div class="form-grid two" style="margin-top: 0.5rem;">
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
            
            @include('partials.location-select', [
                'locations' => $locations,
                'selected' => old('location_id', $item->location_id),
                'label' => 'KUET location',
                'emptyLabel' => 'Choose campus spot...',
                'showPreview' => true,
            ])

            <div style="margin-top: 0.75rem;">
                <label for="item_image">Replace photo <span class="meta">(optional)</span></label>
                <div style="position:relative;">
                    <input id="item_image" type="file" name="item_image" accept="image/*" style="padding:0.5rem; background:var(--paper-2); cursor:pointer;">
                </div>
                @if($item->item_image)
                    <div style="margin-top:0.75rem; border-radius:var(--radius-sm); overflow:hidden; max-width:200px; border:1px solid var(--line);">
                        <img src="{{ asset('storage/'.$item->item_image) }}" alt="Current photo" style="width:100%; display:block;">
                    </div>
                @endif
            </div>
            
            <hr class="divider">
            
            <div class="actions-inline" style="justify-content: flex-end;">
                <a href="{{ route('items.mine') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary" type="submit">Save changes</button>
            </div>
        </form>
    </div>
</section>
@endsection
