@extends('layouts.app')

@section('title', 'My Items')

@section('content')
<section class="section">
    <div class="container">
        <div class="page-header reveal">
            <div>
                <span class="eyebrow">Your posts</span>
                <h2>My items</h2>
                <p>Everything you have reported on FindIt.</p>
            </div>
            @can('create', App\Models\Item::class)
                <a href="{{ route('items.create') }}" class="btn btn-accent">Report new item</a>
            @endcan
        </div>

        @if($items->count())
            <div class="panel table-wrap reveal">
                <table class="data">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td><a href="{{ route('items.show', $item) }}" class="link-accent" style="font-weight:600;">{{ $item->item_name }}</a></td>
                            <td><span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span></td>
                            <td>{{ $item->category->category_name }}</td>
                            <td>{{ $item->location->location_name }}</td>
                            <td><span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span></td>
                            <td style="text-align: right;">
                                <div class="actions-inline" style="justify-content: flex-end;">
                                    @can('update', $item)
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    @endcan
                                    @can('delete', $item)
                                        <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Permanently delete this item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state reveal">
                <h3>No items yet</h3>
                <p>You haven't reported any lost or found items yet.</p>
                <a href="{{ route('items.create') }}" class="btn btn-accent btn-sm" style="margin-top:0.5rem;">Report your first item</a>
            </div>
        @endif
    </div>
</section>
@endsection
