@extends('layouts.app')

@section('title', 'My Items')

@section('content')
<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <span class="eyebrow">Your posts</span>
                <h2>My items</h2>
                <p>Everything you have reported on FindIt.</p>
            </div>
            @can('create', App\Models\Item::class)
                <a href="{{ route('items.create') }}" class="btn btn-accent">Report new</a>
            @endcan
        </div>

        @if($items->count())
            <div class="panel table-wrap">
                <table class="data">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td><a href="{{ route('items.show', $item) }}">{{ $item->item_name }}</a></td>
                            <td><span class="badge {{ strtolower($item->item_type)==='lost' ? 'badge-lost' : 'badge-found' }}">{{ $item->item_type }}</span></td>
                            <td>{{ $item->category->category_name }}</td>
                            <td>{{ $item->location->location_name }}</td>
                            <td><span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span></td>
                            <td>
                                <div class="actions-inline">
                                    @can('update', $item)
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    @endcan
                                    @can('delete', $item)
                                        <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Delete this item?')">
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
            <div class="empty-state">
                <h3>No items yet</h3>
                <p>Report a lost or found item to see it listed here.</p>
                <a href="{{ route('items.create') }}" class="btn btn-accent btn-sm">Report item</a>
            </div>
        @endif
    </div>
</section>
@endsection
