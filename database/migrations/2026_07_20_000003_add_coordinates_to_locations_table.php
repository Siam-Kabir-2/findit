<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('locations', 'latitude')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('description');
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }

        // Seed map pins for KUET, Khulna (Fulbarigate) campus cluster.
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
                ->whereNull('latitude')
                ->update([
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('locations', 'latitude')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn(['latitude', 'longitude']);
            });
        }
    }
};
