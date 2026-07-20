@php
    $selected = (string) ($selected ?? '');
    $required = $required ?? true;
    $allowEmpty = $allowEmpty ?? false;
    $emptyLabel = $emptyLabel ?? 'Select KUET location...';
    $showPreview = $showPreview ?? true;
    $grouped = \App\Models\Location::groupedForSelect($locations);
@endphp

<div class="location-select-block">
    <label for="{{ $id ?? 'location_id' }}">{{ $label ?? 'Location' }}</label>
    <select
        id="{{ $id ?? 'location_id' }}"
        name="{{ $name ?? 'location_id' }}"
        @if($required) required @endif
        @if($showPreview) data-location-select @endif
    >
        @if($allowEmpty)
            <option value="">{{ $emptyLabel }}</option>
        @else
            <option value="" disabled @selected($selected === '')>{{ $emptyLabel }}</option>
        @endif

        @foreach($grouped as $group => $groupLocations)
            <optgroup label="{{ $group }}">
                @foreach($groupLocations as $location)
                    <option
                        value="{{ $location->location_id }}"
                        @selected($selected === (string) $location->location_id)
                        data-lat="{{ $location->latitude }}"
                        data-lng="{{ $location->longitude }}"
                        data-label="{{ $location->location_name }}"
                    >
                        {{ $location->location_name }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>

    @if($showPreview)
        <div class="location-preview" data-location-preview hidden>
            <div
                class="campus-map"
                style="height: 180px;"
                data-map
                data-lat="22.8997"
                data-lng="89.5026"
                data-label="KUET campus"
                data-zoom="17"
            ></div>
            <p class="meta campus-map-hint" data-location-preview-label>Select a location to preview the pin</p>
        </div>
    @endif
</div>
