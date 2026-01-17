<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitLog;
use App\Models\VisitRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Hitung statistik user
        $counts = [
            'users' => User::count(),
            'internal' => User::where('role', 'internal')->count(),
            'security' => User::where('role', 'security')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'visitors' => Visitor::count(),
            'visits_30d' => VisitRequest::where('created_at', '>=', now()->subDays(30))->count(),
            'visits_today' => VisitRequest::whereDate('visit_date', today())->count(),
            'badges' => Badge::count(),
            'badges_available' => Badge::where('status', 'available')->count(),
        ];
        
        // Statistik kunjungan 7 hari terakhir
        $visitStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            $stats = VisitRequest::whereDate('visit_date', $date)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');
                
            $visitStats[] = [
                'date' => $date,
                'registered' => $stats['registered'] ?? 0,
                'checked_in' => $stats['checked_in'] ?? 0,
                'checked_out' => $stats['checked_out'] ?? 0,
                'total' => array_sum($stats->toArray()),
            ];
        }
        
        // Info sistem (sederhana)
        $systemInfo = [
            'db_size' => $this->getDatabaseSize(),
            'storage_free' => $this->getStorageFreeSpace(),
            'log_count' => VisitLog::count(),
            'uptime' => 'Active', // Simplified for now
        ];
        
        // Audit log terbaru
        $recentLogs = VisitLog::with(['visitRequest.visitor', 'performer'])
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.dashboard', compact(
            'counts',
            'visitStats',
            'systemInfo',
            'recentLogs'
        ));
    }
    
    private function getDatabaseSize()
    {
        try {
            // Method 1: Using Laravel query builder
            $result = DB::table('information_schema.tables')
                ->select(DB::raw('SUM(data_length + index_length) / 1024 / 1024 as size_mb'))
                ->where('table_schema', DB::raw('DATABASE()'))
                ->first();
            
            return $result ? round($result->size_mb, 2) . ' MB' : 'N/A';
            
        } catch (\Exception $e) {
            try {
                // Method 2: Simpler approach - just show table counts
                $tables = [
                    'users', 'visitors', 'visit_requests', 
                    'badges', 'badge_assignments', 'visit_logs'
                ];
                
                $totalRecords = 0;
                foreach ($tables as $table) {
                    if (DB::getSchemaBuilder()->hasTable($table)) {
                        $totalRecords += DB::table($table)->count();
                    }
                }
                
                return $totalRecords . ' records';
                
            } catch (\Exception $e) {
                return 'N/A';
            }
        }
    }
    
    private function getStorageFreeSpace()
    {
        try {
            $total = disk_total_space(base_path());
            $free = disk_free_space(base_path());
            
            if ($total > 0) {
                $percentage = round(($free / $total) * 100, 1);
                return $percentage . '% free';
            }
            
            return 'N/A';
            
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}