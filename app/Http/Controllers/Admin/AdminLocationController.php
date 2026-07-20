<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AdminLocationController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(): View
    {
        $locations = Location::query()->orderBy('location_name')->get();

        foreach ($locations as $location) {
            $location->ensureCoordinates();
        }

        $mapPoints = $locations
            ->filter(fn (Location $location) => $location->hasCoordinates())
            ->map(fn (Location $location) => [
                'lat' => (float) $location->latitude,
                'lng' => (float) $location->longitude,
                'label' => $location->location_name,
            ])
            ->values()
            ->all();

        return view('admin.locations', compact('locations', 'mapPoints'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'location_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $latitude = array_key_exists('latitude', $data) && $data['latitude'] !== null && $data['latitude'] !== ''
            ? (float) $data['latitude']
            : null;
        $longitude = array_key_exists('longitude', $data) && $data['longitude'] !== null && $data['longitude'] !== ''
            ? (float) $data['longitude']
            : null;

        if (($latitude === null) xor ($longitude === null)) {
            return back()->withInput()->withErrors([
                'latitude' => 'Provide both latitude and longitude, or leave both empty for auto-pin.',
            ]);
        }

        try {
            $this->plsql->addLocation(
                $data['location_name'],
                $data['description'] ?? null,
                $latitude,
                $longitude
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['location_name' => $e->getMessage()]);
        }

        return back()->with('success', 'Location added — map pin set automatically from the name (or your coordinates).');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->plsql->deleteLocation($id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Location removed.');
    }
}
