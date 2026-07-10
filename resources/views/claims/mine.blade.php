@extends('layouts.app')

@section('title', 'My Claims')

@section('content')
<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <span class="eyebrow">Verification</span>
                <h2>My claims</h2>
                <p>Track claim requests processed by admins via PL/SQL.</p>
            </div>
            <a href="{{ route('items.index') }}" class="btn btn-ghost">Browse board</a>
        </div>

        @if(count($claims))
            <div class="panel table-wrap">
                <table class="data">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Claim status</th>
                        <th>Item status</th>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($claims as $claim)
                        <tr>
                            <td><a href="{{ route('items.show', $claim->item_id) }}">{{ $claim->item_name }}</a></td>
                            <td>{{ $claim->item_type }}</td>
                            <td><span class="badge badge-{{ strtolower($claim->claim_status) }}">{{ $claim->claim_status }}</span></td>
                            <td><span class="badge badge-{{ strtolower($claim->item_status) }}">{{ $claim->item_status }}</span></td>
                            <td>{{ $claim->claim_message ?: '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <h3>No claims yet</h3>
                <p>Find an item on the board and submit proof to start a claim.</p>
                <a href="{{ route('items.index') }}" class="btn btn-accent btn-sm">Browse items</a>
            </div>
        @endif
    </div>
</section>
@endsection
