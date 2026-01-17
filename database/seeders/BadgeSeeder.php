<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('badges')->insert([
            [
                'badge_code' => 'BG-001',
                'access_area' => 'Gedung Utama, Lantai 1-3',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'badge_code' => 'BG-002',
                'access_area' => 'Gedung Utama, Lantai 1-3',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'badge_code' => 'BG-003',
                'access_area' => 'Gedung Utama, Lantai 1-5',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'badge_code' => 'BG-004',
                'access_area' => 'Area Terbatas, R&D',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'badge_code' => 'BG-005',
                'access_area' => 'Area Publik, Lobi',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}