<?php

namespace App\Models;

use App\Services\CampusGeocoder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $table = 'locations';

    protected $primaryKey = 'location_id';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'location_name',
        'description',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'location_id', 'location_id');
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null
            && $this->longitude !== null
            && is_finite((float) $this->latitude)
            && is_finite((float) $this->longitude);
    }

    /**
     * Auto-resolve and persist map coordinates from the location name when missing.
     */
    public function ensureCoordinates(?CampusGeocoder $geocoder = null): self
    {
        if ($this->hasCoordinates()) {
            return $this;
        }

        $geocoder ??= app(CampusGeocoder::class);
        [$lat, $lng] = $geocoder->resolve((string) $this->location_name);

        $this->forceFill([
            'latitude' => $lat,
            'longitude' => $lng,
        ])->save();

        return $this;
    }

    public function campusGroup(): string
    {
        $name = mb_strtolower((string) $this->location_name);

        if (str_contains($name, 'hall') || str_contains($name, 'rokeya')) {
            return 'Residential halls';
        }

        if (str_contains($name, 'gate') || str_contains($name, 'transport') || str_contains($name, 'bus')) {
            return 'Entrance & transport';
        }

        if (str_contains($name, 'building') || str_contains($name, 'library') || str_contains($name, 'eee') || str_contains($name, 'cse') || str_contains($name, 'civil') || str_contains($name, 'mechanical') || str_contains($name, 'computer center') || str_contains($name, 'academic')) {
            return 'Academic';
        }

        return 'Campus facilities';
    }

    /**
     * @param  \Illuminate\Support\Collection<int, self>|iterable<self>  $locations
     * @return array<string, \Illuminate\Support\Collection<int, self>>
     */
    public static function groupedForSelect(iterable $locations): array
    {
        $order = ['Academic', 'Campus facilities', 'Residential halls', 'Entrance & transport'];

        return collect($locations)
            ->sortBy('location_name')
            ->groupBy(fn (self $location) => $location->campusGroup())
            ->sortBy(fn ($group, $key) => array_search($key, $order, true) === false ? 99 : array_search($key, $order, true))
            ->all();
    }
}
