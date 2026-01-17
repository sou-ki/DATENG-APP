<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name'        => 'Admin Sistem',
                'email'       => 'admin@pag.co.id',
                'password'    => Hash::make('password'),
                'role'        => 'admin',
                'division_id' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Petugas Security',
                'email'       => 'security@pag.co.id',
                'password'    => Hash::make('password'),
                'role'        => 'security',
                'division_id' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
