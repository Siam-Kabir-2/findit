@extends('layouts.admin')

@section('title', 'Categories')
@section('heading', 'Manage Categories')

@section('content')
<div class="admin-grid-two">
    <form method="POST" action="{{ route('admin.categories.store') }}" class="panel form-grid">
        @csrf
        <div class="panel-title" style="margin-bottom:0;">
            <h3 class="font-display" style="color:var(--primary);font-size:1.15rem;">Add new category</h3>
        </div>
        <p class="meta" style="margin-top:-0.5rem;margin-bottom:0.5rem;">Create categories to help users classify and filter items easily.</p>

        <div>
            <label for="category_name">Category name</label>
            <input id="category_name" name="category_name" value="{{ old('category_name') }}" required placeholder="e.g. Electronics">
        </div>
        <button class="btn btn-primary" type="submit">Add category</button>
    </form>

    <div class="panel table-wrap">
        <div style="margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h3 class="font-display" style="margin:0;font-size:1.15rem;">Configured Categories</h3>
                <p class="meta" style="margin:0;margin-top:0.25rem;">Available classification types</p>
            </div>
            <span class="badge" style="background:var(--paper-2);color:var(--ink);">{{ count($categories) }} total</span>
        </div>
        
        @if(count($categories))
            <table class="data">
                <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Name</th>
                    <th style="width: 100px; text-align: right;">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td class="meta">#{{ $category->category_id }}</td>
                        <td><strong>{{ $category->category_name }}</strong></td>
                        <td style="text-align: right;">
                            <form method="POST" action="{{ route('admin.categories.destroy', $category->category_id) }}" onsubmit="return confirm('Are you sure you want to delete this category? Items associated with it might be affected.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <h3>No categories yet</h3>
                <p>Use the form on the left to add your first classification category.</p>
            </div>
        @endif
    </div>
</div>
@endsection
