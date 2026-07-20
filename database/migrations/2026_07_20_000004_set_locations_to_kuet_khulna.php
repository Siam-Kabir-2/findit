<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('locations', 'latitude')) {
            return;
        }

        // KUET, Fulbarigate, Khulna — campus cluster around 22.8997, 89.5026
        $coords = [
            'Main Library' => [22.90040, 89.50280],
            'Student Cafeteria' => [22.89960, 89.50180],
            'Computer Lab A' => [22.90080, 89.50140],
            'Parking Lot B' => [22.89880, 89.50360],
            'Student Union' => [22.89990, 89.50100],
            'Science Building' => [22.90120, 89.50320],
            'Sports Complex' => [22.89840, 89.50220],
            'Dorm Hall A' => [22.90160, 89.50260],
        ];

        foreach ($coords as $name => [$lat, $lng]) {
            DB::table('locations')
                ->where('location_name', $name)
                ->update([
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);
        }
    }

    public function down(): void
    {
        // Irreversible coordinate remap; no-op.
    }
};
