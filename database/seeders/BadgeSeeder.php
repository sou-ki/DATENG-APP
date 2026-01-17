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
                'badge_code'  => 'BDG-001',
                'access_area' => 'Area Kantor',
                'status'      => 'available',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'badge_code'  => 'BDG-002',
                'access_area' => 'Area Gudang',
                'status'      => 'available',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'badge_code'  => 'BDG-003',
                'access_area' => 'Area Produksi',
                'status'      => 'available',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
