@extends('layouts.admin')

@section('title', 'Locations')
@section('heading', 'Manage Locations')

@section('content')
<div class="admin-grid-two">
    <form method="POST" action="{{ route('admin.locations.store') }}" class="panel form-grid">
        @csrf
        <div class="panel-title" style="margin-bottom:0;">
            <h3 class="font-display" style="color:var(--primary);font-size:1.15rem;">Add new location</h3>
        </div>
        <p class="meta" style="margin-top:-0.5rem;margin-bottom:0.5rem;">
            Enter a name — the map pin is set automatically for KUET, Khulna.
            Coordinates below are optional overrides.
        </p>

        <div>
            <label for="location_name">Location name</label>
            <input id="location_name" name="location_name" value="{{ old('location_name') }}" required placeholder="e.g. Central Library">
        </div>
        <div>
            <label for="description">Short description</label>
            <textarea id="description" name="description" placeholder="Specify floor, hall, or landmarks...">{{ old('description') }}</textarea>
        </div>
        <div class="form-row-2">
            <div>
                <label for="latitude">Latitude <span class="meta">(optional)</span></label>
                <input id="latitude" name="latitude" type="number" step="any" min="-90" max="90" value="{{ old('latitude') }}" placeholder="Auto from name">
            </div>
            <div>
                <label for="longitude">Longitude <span class="meta">(optional)</span></label>
                <input id="longitude" name="longitude" type="number" step="any" min="-180" max="180" value="{{ old('longitude') }}" placeholder="Auto from name">
            </div>
        </div>

        @include('partials.location-map', [
            'location' => (object) [
                'location_name' => old('location_name', 'KUET campus'),
                'latitude' => old('latitude', 22.8997),
                'longitude' => old('longitude', 89.5026),
            ],
            'picker' => true,
            'latInput' => '#latitude',
            'lngInput' => '#longitude',
            'height' => '220px',
            'zoom' => 16,
        ])

        <button class="btn btn-primary" type="submit">Add location</button>
    </form>

    <div class="panel table-wrap">
        <div style="margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h3 class="font-display" style="margin:0;font-size:1.15rem;">Configured Locations</h3>
                <p class="meta" style="margin:0;margin-top:0.25rem;">Zones available for reporting · map pins for item pages</p>
            </div>
            <span class="badge" style="background:var(--paper-2);color:var(--ink);">{{ count($locations) }} total</span>
        </div>

        @if(count($locations))
            <div class="campus-map-wrap" style="margin-bottom:1.25rem;">
                <div
                    class="campus-map"
                    style="height: 260px;"
                    data-map-multi
                    data-points='@json($mapPoints)'
                    data-zoom="15"
                    role="img"
                    aria-label="Map of all campus locations"
                ></div>
                <p class="meta campus-map-hint">All locations with map pins</p>
            </div>

            <table class="data">
                <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Name</th>
                    <th>Map pin</th>
                    <th style="width: 100px; text-align: right;">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($locations as $location)
                    <tr>
                        <td class="meta">#{{ $location->location_id }}</td>
                        <td>
                            <strong>{{ $location->location_name }}</strong>
                            <div class="meta">{{ $location->description ?: '—' }}</div>
                        </td>
                        <td class="meta">
                            @if($location->latitude !== null && $location->longitude !== null)
                                {{ number_format((float) $location->latitude, 5) }},
                                {{ number_format((float) $location->longitude, 5) }}
                            @else
                                <span style="color:var(--warn,#b87e00);">No pin</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form method="POST" action="{{ route('admin.locations.destroy', $location->location_id) }}" onsubmit="return confirm('Are you sure you want to delete this location? Items associated with it might be affected.')">
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
                <h3>No locations yet</h3>
                <p>Use the form on the left to add your first campus location.</p>
            </div>
        @endif
    </div>
</div>
@endsection
