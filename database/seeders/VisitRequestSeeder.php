<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitRequestSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->where('role', 'internal')->get();
        $visitors = DB::table('visitors')->get();
        $divisions = DB::table('divisions')->get();

        // Sesuaikan dengan enum di migration
        $visitTypes = ['antar_barang', 'ambil_barang', 'kunjungan', 'inspeksi', 'lainnya'];
        $statuses = ['registered', 'checked_in', 'checked_out', 'rejected'];
        
        // Array untuk purpose yang sesuai dengan visit_type
        $purposes = [
            'antar_barang' => ['Pengiriman dokumen', 'Pengiriman paket', 'Pengiriman barang'],
            'ambil_barang' => ['Pengambilan dokumen', 'Pengambilan barang', 'Pengambilan paket'],
            'kunjungan' => ['Meeting rutin', 'Konsultasi', 'Presentasi', 'Rapat koordinasi'],
            'inspeksi' => ['Inspeksi berkala', 'Audit internal', 'Pengecekan fasilitas'],
            'lainnya' => ['Wawancara kerja', 'Training', 'Survey', 'Koordinasi proyek']
        ];

        // Buat 10 data dummy untuk berbagai tanggal
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $visitor = $visitors->random();
            $division = $divisions->random();
            $status = $statuses[array_rand($statuses)];
            $visitType = $visitTypes[array_rand($visitTypes)];
            
            // Ambil purpose yang sesuai dengan visit_type
            $purposeOptions = $purposes[$visitType];
            $purpose = $purposeOptions[array_rand($purposeOptions)];
            
            $visitDate = Carbon::today()->addDays(rand(-5, 5));
            $startTime = Carbon::createFromTime(rand(8, 16), rand(0, 3) * 15, 0);
            $endTime = (clone $startTime)->addHours(rand(1, 3));

            DB::table('visit_requests')->insert([
                'visitor_id' => $visitor->id,
                'division_id' => $division->id,
                'purpose' => $purpose,
                'visit_type' => $visitType,
                'visit_date' => $visitDate,
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'letter_path' => null, // nullable
                'status' => $status,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat beberapa data untuk hari ini dengan status registered
        for ($i = 0; $i < 3; $i++) {
            $user = $users->random();
            $visitor = $visitors->random();
            $division = $divisions->random();
            $visitType = $visitTypes[array_rand(['kunjungan', 'antar_barang', 'ambil_barang'])];
            
            $purposeOptions = $purposes[$visitType];
            $purpose = $purposeOptions[array_rand($purposeOptions)];
            
            $startTime = Carbon::createFromTime(rand(9, 17), rand(0, 3) * 15, 0);
            $endTime = (clone $startTime)->addHours(rand(1, 2));

            DB::table('visit_requests')->insert([
                'visitor_id' => $visitor->id,
                'division_id' => $division->id,
                'purpose' => $purpose,
                'visit_type' => $visitType,
                'visit_date' => Carbon::today(),
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'letter_path' => null,
                'status' => 'registered',
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat beberapa data untuk hari ini dengan status checked_in (sedang berkunjung)
        for ($i = 0; $i < 2; $i++) {
            $user = $users->random();
            $visitor = $visitors->random();
            $division = $divisions->random();
            $visitType = $visitTypes[array_rand(['kunjungan', 'inspeksi', 'lainnya'])];
            
            $purposeOptions = $purposes[$visitType];
            $purpose = $purposeOptions[array_rand($purposeOptions)];
            
            // Untuk checked_in, waktu mulai harus sudah lewat
            $startTime = Carbon::now()->subHours(rand(1, 3));
            $endTime = (clone $startTime)->addHours(rand(1, 3));

            DB::table('visit_requests')->insert([
                'visitor_id' => $visitor->id,
                'division_id' => $division->id,
                'purpose' => $purpose,
                'visit_type' => $visitType,
                'visit_date' => Carbon::today(),
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'letter_path' => null,
                'status' => 'checked_in',
                'created_by' => $user->id,
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ]);
        }
    }
}