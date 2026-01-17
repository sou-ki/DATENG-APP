<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\VisitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActiveVisitController extends Controller
{
    public function index()
    {
        // Get active visits with related data (hanya yang punya badge assignment)
        $activeVisits = VisitRequest::with([
            'visitor', 
            'division',
            'badgeAssignment.badge',
            'badgeAssignment.assigner'
        ])
        ->where('status', 'checked_in')
        ->whereHas('badgeAssignment') // Hanya yang punya badge assignment
        ->orderBy('created_at', 'desc')
        ->get();
        
        // Get visits without badge assignment (problematic)
        $visitsWithoutBadge = VisitRequest::with('visitor')
            ->where('status', 'checked_in')
            ->doesntHave('badgeAssignment')
            ->get();
        
        // Calculate today's statistics
        $todayStats = [
            'total' => VisitRequest::whereDate('visit_date', today())->count(),
            'active' => VisitRequest::where('status', 'checked_in')->count(),
            'completed' => VisitRequest::whereDate('visit_date', today())
                ->where('status', 'checked_out')
                ->count(),
            'without_badge' => $visitsWithoutBadge->count(),
        ];
        
        // Calculate average duration
        $avgDuration = DB::table('badge_assignments')
            ->join('visit_requests', 'badge_assignments.visit_request_id', '=', 'visit_requests.id')
            ->whereDate('visit_requests.visit_date', today())
            ->whereNotNull('badge_assignments.returned_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, assigned_at, returned_at)) as avg_minutes')
            ->first();
            
        $todayStats['avg_duration'] = $avgDuration->avg_minutes 
            ? round($avgDuration->avg_minutes) . 'm' 
            : '0m';
        
        return view('security.active-visits', compact(
            'activeVisits', 
            'todayStats',
            'visitsWithoutBadge'
        ));
    }
}