@extends('layouts.admin')

@section('title', 'Items')
@section('heading', 'Manage Items')

@section('content')
<div class="panel table-wrap">
    <table class="data">
        <thead>
        <tr>
            <th>Item</th>
            <th>Owner</th>
            <th>Type</th>
            <th>Place</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>
                    <a href="{{ route('items.show', $item->item_id) }}" style="color:var(--accent);font-weight:600;">{{ $item->item_name }}</a>
                    <div class="meta">{{ $item->category_name }}</div>
                </td>
                <td>{{ $item->user_name }}</td>
                <td><span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span></td>
                <td>{{ $item->location_name }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.items.status', $item->item_id) }}" class="actions-inline">
                        @csrf
                        <select name="status" style="min-width:120px;">
                            @foreach(['PENDING','FOUND','CLAIMED','RETURNED','REJECTED'] as $status)
                                <option value="{{ $status }}" @selected($item->status===$status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-ghost btn-sm" type="submit">Update</button>
                    </form>
                </td>
                <td>
                    <form method="POST" action="{{ route('admin.items.destroy', $item->item_id) }}" onsubmit="return confirm('Delete item?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">No items found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
