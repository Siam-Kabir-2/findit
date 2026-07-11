@extends('layouts.admin')

@section('title', 'Locations')
@section('heading', 'Locations')

@section('content')
<div style="display:grid;grid-template-columns:320px 1fr;gap:1rem;">
    <form method="POST" action="{{ route('admin.locations.store') }}" class="panel form-grid">
        @csrf
        <h3 class="font-display" style="margin:0;color:var(--accent);">Add location</h3>
        <div>
            <label for="location_name">Name</label>
            <input id="location_name" name="location_name" value="{{ old('location_name') }}" required>
        </div>
        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>
        <button class="btn btn-primary" type="submit">Add via PL/SQL</button>
    </form>

    <div class="panel table-wrap">
        <table class="data">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($locations as $location)
                <tr>
                    <td>{{ $location->location_id }}</td>
                    <td>{{ $location->location_name }}</td>
                    <td>{{ $location->description }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.locations.destroy', $location->location_id) }}" onsubmit="return confirm('Delete location?')">
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
