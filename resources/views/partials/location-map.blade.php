@php
    $height = $height ?? '280px';
    $zoom = $zoom ?? 17;
    $picker = $picker ?? false;
    $latInput = $latInput ?? null;
    $lngInput = $lngInput ?? null;

    $hasCoords = isset($location)
        && $location->latitude !== null
        && $location->longitude !== null
        && is_numeric($location->latitude)
        && is_numeric($location->longitude);

    $lat = $hasCoords ? (float) $location->latitude : 22.8997;
    $lng = $hasCoords ? (float) $location->longitude : 89.5026;
    $label = $location->location_name ?? 'Campus location';
@endphp

@if($picker || $hasCoords)
    <div class="campus-map-wrap">
        <div
            class="campus-map"
            style="height: {{ $height }};"
            @if($picker)
                data-map-picker
                data-lat-input="{{ $latInput }}"
                data-lng-input="{{ $lngInput }}"
            @else
                data-map
            @endif
            data-lat="{{ $lat }}"
            data-lng="{{ $lng }}"
            data-label="{{ e($label) }}"
            data-zoom="{{ $zoom }}"
            role="img"
            aria-label="Map showing {{ e($label) }}"
        ></div>
        @if($picker)
            <p class="meta campus-map-hint">Click the map to set the pin. Coordinates fill in automatically.</p>
        @else
            <p class="meta campus-map-hint">
                {{ $label }}
                ·
                <a
                    class="link-accent"
                    href="https://www.openstreetmap.org/?mlat={{ $lat }}&mlon={{ $lng }}#map={{ $zoom }}/{{ $lat }}/{{ $lng }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >Open in OpenStreetMap</a>
            </p>
        @endif
    </div>
@else
    <div class="campus-map-empty">
        <p class="meta">No map pin yet for this location. An admin can add coordinates under Locations.</p>
    </div>
@endif
