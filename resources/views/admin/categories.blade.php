@extends('layouts.admin')

@section('title', 'Categories')
@section('heading', 'Categories')

@section('content')
<div style="display:grid;grid-template-columns:320px 1fr;gap:1rem;">
    <form method="POST" action="{{ route('admin.categories.store') }}" class="panel form-grid">
        @csrf
        <h3 class="font-display" style="margin:0;color:var(--accent);">Add category</h3>
        <div>
            <label for="category_name">Name</label>
            <input id="category_name" name="category_name" value="{{ old('category_name') }}" required>
        </div>
        <button class="btn btn-primary" type="submit">Add via PL/SQL</button>
    </form>

    <div class="panel table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->category_id }}</td>
                    <td>{{ $category->category_name }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category->category_id) }}" onsubmit="return confirm('Delete category?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
