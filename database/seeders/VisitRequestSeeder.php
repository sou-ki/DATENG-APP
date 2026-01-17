<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\User;
use App\Models\VisitRequest;
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
                'letter_path' => null,
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
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
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
        
        // ===== PERBAIKAN BAGIAN INI =====
        // Create badge assignments for checked_in visits
        $checkedInVisits = DB::table('visit_requests')->where('status', 'checked_in')->get();
        $availableBadges = DB::table('badges')->where('status', 'available')->get()->toArray();
        $securityUsers = DB::table('users')->where('role', 'security')->get();
        
        if ($securityUsers->isNotEmpty() && !empty($availableBadges)) {
            $securityId = $securityUsers->first()->id;
            
            foreach ($checkedInVisits as $visit) {
                if (!empty($availableBadges)) {
                    $badge = array_shift($availableBadges);
                    
                    // Update badge status to in_use
                    DB::table('badges')->where('id', $badge->id)->update(['status' => 'in_use']);
                    
                    // Create badge assignment
                    DB::table('badge_assignments')->insert([
                        'visit_request_id' => $visit->id,
                        'badge_id' => $badge->id,
                        'assigned_by' => $securityId,
                        'assigned_at' => now()->subHours(rand(1, 3)),
                        'returned_at' => null,
                    ]);
                    
                    // Create visit log for check-in (TANPA created_at & updated_at)
                    DB::table('visit_logs')->insert([
                        'visit_request_id' => $visit->id,
                        'action' => 'check_in',
                        'performed_by' => $securityId,
                        'timestamp' => now()->subHours(rand(1, 3)),
                        'notes' => 'Check-in oleh security',
                    ]);
                }
            }
        }
        
        // Create some visit logs for checked_out visits
        $checkedOutVisits = DB::table('visit_requests')->where('status', 'checked_out')->limit(3)->get();
        
        foreach ($checkedOutVisits as $visit) {
            if ($securityUsers->isNotEmpty()) {
                $securityId = $securityUsers->first()->id;
                
                // Create visit log for check-out
                DB::table('visit_logs')->insert([
                    'visit_request_id' => $visit->id,
                    'action' => 'check_out',
                    'performed_by' => $securityId,
                    'timestamp' => now()->subHours(rand(1, 2)),
                    'notes' => 'Check-out selesai',
                ]);
            }
        }
    }
}