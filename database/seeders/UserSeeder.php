<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = DB::table('divisions')->get();

        // Admin User (tidak punya divisi)
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'division_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Internal Users (punya divisi)
        DB::table('users')->insert([
            [
                'name' => 'Budi Santoso',
                'email' => 'hrd@company.com', // Sesuai dengan blueprint
                'password' => Hash::make('password'),
                'role' => 'internal',
                'division_id' => $divisions->where('division_name', 'Human Resource')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'it@company.com', // Sesuai dengan blueprint
                'password' => Hash::make('password'),
                'role' => 'internal',
                'division_id' => $divisions->where('division_name', 'IT Support')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ops@company.com', // Sesuai dengan blueprint
                'password' => Hash::make('password'),
                'role' => 'internal',
                'division_id' => $divisions->where('division_name', 'Operasional')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rina Dewi',
                'email' => 'rina@company.com',
                'password' => Hash::make('password'),
                'role' => 'internal',
                'division_id' => $divisions->where('division_name', 'Human Resource')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'joko@company.com',
                'password' => Hash::make('password'),
                'role' => 'internal',
                'division_id' => $divisions->where('division_name', 'IT Support')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Security Users (tidak punya divisi)
        DB::table('users')->insert([
            [
                'name' => 'Security Officer 1',
                'email' => 'security1@company.com',
                'password' => Hash::make('password'),
                'role' => 'security',
                'division_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Security Officer 2',
                'email' => 'security2@company.com',
                'password' => Hash::make('password'),
                'role' => 'security',
                'division_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}