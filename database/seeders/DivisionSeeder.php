<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('divisions')->insert([
            [
                'division_name' => 'Human Resource',
                'description'   => 'Pengelolaan SDM dan administrasi karyawan',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'division_name' => 'IT Support',
                'description'   => 'Pengelolaan sistem dan infrastruktur IT',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'division_name' => 'Operasional',
                'description'   => 'Kegiatan operasional lapangan',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
