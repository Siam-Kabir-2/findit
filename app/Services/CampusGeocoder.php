<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Resolves campus location names to map pins for KUET, Khulna.
 * Uses OpenStreetMap Nominatim when available, otherwise a stable campus fallback.
 */
class CampusGeocoder
{
    public const CAMPUS_LAT = 22.8997;

    public const CAMPUS_LNG = 89.5026;

    /**
     * @return array{0: float, 1: float}
     */
    public function resolve(string $locationName): array
    {
        $name = trim($locationName);

        if ($name === '') {
            return [self::CAMPUS_LAT, self::CAMPUS_LNG];
        }

        $queries = [
            $name.', KUET, Fulbarigate, Khulna, Bangladesh',
            $name.', Khulna University of Engineering and Technology, Bangladesh',
        ];

        foreach ($queries as $query) {
            $hit = $this->nominatimSearch($query);
            if ($hit && $this->nearCampus($hit[0], $hit[1])) {
                return $hit;
            }
        }

        return $this->campusPinFor($name);
    }

    /**
     * Deterministic pin near KUET campus so buildings don't all stack on one point.
     *
     * @return array{0: float, 1: float}
     */
    public function campusPinFor(string $locationName): array
    {
        $hash = crc32(mb_strtolower(trim($locationName)));
        // ~±150m spread around campus center
        $latOff = (($hash % 300) - 150) / 100000;
        $lngOff = (((int) ($hash / 300) % 300) - 150) / 100000;

        return [
            round(self::CAMPUS_LAT + $latOff, 7),
            round(self::CAMPUS_LNG + $lngOff, 7),
        ];
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    protected function nominatimSearch(string $query): ?array
    {
        try {
            $response = Http::timeout(4)
                ->withHeaders([
                    'User-Agent' => 'FindIt-KUET-LostAndFound/1.0 (campus academic project)',
                    'Accept-Language' => 'en',
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'bd',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $row = $response->json('0');
            if (! is_array($row) || ! isset($row['lat'], $row['lon'])) {
                return null;
            }

            $lat = (float) $row['lat'];
            $lng = (float) $row['lon'];

            if (! is_finite($lat) || ! is_finite($lng)) {
                return null;
            }

            return [round($lat, 7), round($lng, 7)];
        } catch (Throwable $e) {
            Log::debug('Campus geocode skipped: '.$e->getMessage());

            return null;
        }
    }

    protected function nearCampus(float $lat, float $lng): bool
    {
        // ~8km box — keeps results around Khulna / KUET, drops far mismatches
        return abs($lat - self::CAMPUS_LAT) <= 0.08
            && abs($lng - self::CAMPUS_LNG) <= 0.08;
    }
}
