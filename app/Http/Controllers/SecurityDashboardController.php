<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\VisitLog;
use App\Models\VisitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecurityDashboardController extends Controller
{
    public function index()
    {
        // Statistik untuk hari ini
        $todayCounts = [
            'total' => VisitRequest::whereDate('visit_date', today())->count(),
            'registered' => VisitRequest::whereDate('visit_date', today())
                ->where('status', 'registered')
                ->count(),
            'checked_in' => VisitRequest::where('status', 'checked_in')->count(),
            'checked_out' => VisitRequest::whereDate('visit_date', today())
                ->where('status', 'checked_out')
                ->count(),
        ];
        
        // Kunjungan yang menunggu check-in hari ini
        $pendingVisits = VisitRequest::with('visitor')
            ->whereDate('visit_date', today())
            ->where('status', 'registered')
            ->orderBy('start_time')
            ->limit(10)
            ->get();
            
        // Kunjungan aktif (checked_in) dengan eager loading
        $activeVisits = VisitRequest::with([
                'visitor', 
                'badgeAssignment' => function($query) {
                    $query->with('badge');
                }
            ])
            ->where('status', 'checked_in')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Statistik badge
        $badgeCounts = [
            'total' => Badge::count(),
            'available' => Badge::where('status', 'available')->count(),
            'in_use' => Badge::where('status', 'in_use')->count(),
        ];
        
        // Aktivitas terakhir dengan eager loading
        $recentLogs = VisitLog::with([
                'visitRequest.visitor',
                'performer' => function($query) {
                    $query->select('id', 'name', 'role');
                }
            ])
            ->orderBy('timestamp', 'desc')
            ->limit(5)
            ->get();
            
        return view('security.dashboard', compact(
            'todayCounts',
            'pendingVisits',
            'activeVisits',
            'badgeCounts',
            'recentLogs'
        ));
    }
}