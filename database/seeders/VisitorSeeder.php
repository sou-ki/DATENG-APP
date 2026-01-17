<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('visitors')->insert([
            [
                'full_name' => 'Andi Wijaya',
                'identity_number' => '3273010101010001',
                'institution' => 'PT. Supplier Teknik',
                'phone_number' => '081234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Dewi Lestari',
                'identity_number' => '3273010202020002',
                'institution' => 'CV. Jaya Abadi',
                'phone_number' => '082345678901',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Rudi Hartono',
                'identity_number' => '3273010303030003',
                'institution' => 'Universitas Negeri',
                'phone_number' => '083456789012',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Maya Sari',
                'identity_number' => '3273010404040004',
                'institution' => 'Bank Central',
                'phone_number' => '084567890123',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Eko Prasetyo',
                'identity_number' => '3273010505050005',
                'institution' => 'Dinas Pemerintah',
                'phone_number' => '085678901234',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}