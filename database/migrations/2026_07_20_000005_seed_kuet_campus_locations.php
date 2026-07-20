<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('locations')) {
            return;
        }

        $campus = [
            // Renames from older generic sample names → KUET names
            'Main Library' => 'Central Library',
            'Student Cafeteria' => 'Central Cafeteria',
            'Computer Lab A' => 'Central Computer Center',
            'Parking Lot B' => 'Main Gate (Fulbarigate)',
            'Student Union' => 'Student Welfare Center',
            'Science Building' => 'Academic Building',
            'Sports Complex' => 'Playground',
            'Dorm Hall A' => 'Fazlul Haque Hall',
        ];

        foreach ($campus as $oldName => $newName) {
            $old = DB::table('locations')->where('location_name', $oldName)->first();
            if (! $old) {
                continue;
            }

            $newExists = DB::table('locations')->where('location_name', $newName)->exists();
            if ($newExists) {
                // Keep the KUET-named row; point items at it, then drop the old generic row.
                DB::table('items')
                    ->where('location_id', $old->location_id)
                    ->update([
                        'location_id' => DB::table('locations')->where('location_name', $newName)->value('location_id'),
                    ]);
                DB::table('locations')->where('location_id', $old->location_id)->delete();
            } else {
                DB::table('locations')
                    ->where('location_id', $old->location_id)
                    ->update(['location_name' => $newName]);
            }
        }

        $locations = [
            ['Central Library', 'KUET Central Library', 22.90055, 89.50295],
            ['Central Cafeteria', 'Student Welfare Center cafeteria', 22.89985, 89.50175],
            ['Central Computer Center', 'Central Computer Center (CCC)', 22.90090, 89.50155],
            ['Main Gate (Fulbarigate)', 'Main campus entrance area', 22.89870, 89.50380],
            ['Student Welfare Center', 'SWC — cafeteria, indoor games, open stage', 22.89995, 89.50125],
            ['Academic Building', 'Main academic / classroom buildings', 22.90115, 89.50245],
            ['Playground', 'Central playground and sports field', 22.89855, 89.50215],
            ['Fazlul Haque Hall', 'Residential hall', 22.90185, 89.50185],
            ['Central Mosque', 'KUET Central Mosque', 22.90025, 89.50105],
            ['Administrative Building', 'Admin / registrar offices', 22.90070, 89.50315],
            ['Medical Center', 'Campus medical center', 22.89940, 89.50255],
            ['Gymnasium & Swimming Pool', 'Gym and swimming pool complex', 22.89895, 89.50155],
            ['Open Stage (Mukto Mancha)', 'Open stage at Student Welfare Center', 22.89970, 89.50140],
            ['Lalan Shah Hall', 'Residential hall', 22.90210, 89.50235],
            ['Khan Jahan Ali Hall', 'Residential hall', 22.90235, 89.50155],
            ['Dr. M.A. Rashid Hall', 'Residential hall', 22.90155, 89.50095],
            ['Rokeya Hall', 'Female residential hall', 22.90195, 89.50355],
            ['Amar Ekushey Hall', 'Residential hall', 22.90245, 89.50305],
            ['Bangabandhu Hall', 'Bangabandhu Sheikh Mujibur Rahman Hall', 22.90270, 89.50255],
            ['Shaheed Smriti Hall', 'Residential hall', 22.90125, 89.50405],
            ['New Academic Building', 'Newer academic classroom block', 22.90145, 89.50115],
            ['EEE Building', 'Electrical & Electronic Engineering dept.', 22.90085, 89.50265],
            ['CSE Building', 'Computer Science & Engineering dept.', 22.90105, 89.50205],
            ['Civil Building', 'Civil Engineering department', 22.90035, 89.50225],
            ['Mechanical Building', 'Mechanical Engineering department', 22.90015, 89.50305],
            ['Transport / Bus Stand', 'Campus transport waiting area', 22.89885, 89.50335],
        ];

        foreach ($locations as [$name, $description, $lat, $lng]) {
            $existing = DB::table('locations')->where('location_name', $name)->first();
            if ($existing) {
                DB::table('locations')->where('location_id', $existing->location_id)->update([
                    'description' => $description,
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);
            } else {
                DB::table('locations')->insert([
                    'location_name' => $name,
                    'description' => $description,
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Keep seeded campus locations; irreversible by design.
    }
};
