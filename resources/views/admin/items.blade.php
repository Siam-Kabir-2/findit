@extends('layouts.admin')

@section('title', 'Items')
@section('heading', 'Manage Items')

@section('content')
<div class="panel table-wrap">
    <div style="margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h3 class="font-display" style="margin:0;font-size:1.15rem;">Reported Listings</h3>
            <p class="meta" style="margin:0;margin-top:0.25rem;">Global overview of all items reported on campus.</p>
        </div>
        <span class="badge" style="background:var(--paper-2);color:var(--ink);">{{ count($items) }} total items</span>
    </div>
    
    @if(count($items))
        <table class="data">
            <thead>
            <tr>
                <th>Item</th>
                <th>Owner</th>
                <th>Type</th>
                <th>Place</th>
                <th>Status</th>
                <th style="text-align: right;">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>
                        <a href="{{ route('items.show', $item->item_id) }}" class="link-accent" style="display:block;margin-bottom:0.15rem;">{{ $item->item_name }}</a>
                        <div class="meta">{{ $item->category_name }}</div>
                    </td>
                    <td>
                        <div style="font-weight:500;">{{ $item->user_name }}</div>
                    </td>
                    <td>
                        <span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span>
                    </td>
                    <td>{{ $item->location_name }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.items.status', $item->item_id) }}" class="actions-inline">
                            @csrf
                            <select name="status" style="min-width:120px;padding:0.4rem 0.6rem;font-size:0.85rem;height:auto;">
                                @foreach(['PENDING','FOUND','CLAIMED','RETURNED','REJECTED'] as $status)
                                    <option value="{{ $status }}" @selected($item->status===$status)>{{ $status }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-ghost btn-sm" style="padding:0.4rem 0.6rem;" type="submit">Update</button>
                        </form>
                    </td>
                    <td style="text-align: right;">
                        <form method="POST" action="{{ route('admin.items.destroy', $item->item_id) }}" onsubmit="return confirm('Are you sure you want to permanently delete this item?')">
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
            <h3>No items found</h3>
            <p>There are currently no lost or found items reported in the system.</p>
        </div>
    @endif
</div>
@endsection
