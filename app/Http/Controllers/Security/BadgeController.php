<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\BadgeAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    /**
     * Display badge management page
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        
        // Query badges
        $query = Badge::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('badge_code', 'like', "%{$search}%")
                  ->orWhere('access_area', 'like', "%{$search}%");
            });
        }
        
        if ($status && in_array($status, ['available', 'in_use'])) {
            $query->where('status', $status);
        }
        
        $badges = $query->orderBy('badge_code')->paginate(20);
        
        // Statistics
        $stats = [
            'total' => Badge::count(),
            'available' => Badge::where('status', 'available')->count(),
            'in_use' => Badge::where('status', 'in_use')->count(),
        ];
        
        // Currently assigned badges with visitor info
        $assignedBadges = BadgeAssignment::with([
            'badge',
            'visitRequest.visitor',
            'visitRequest.division',
            'assigner'
        ])
        ->whereNull('returned_at')
        ->orderBy('assigned_at', 'desc')
        ->get();
        
        // Recent badge activities
        $recentActivities = BadgeAssignment::with([
            'badge',
            'visitRequest.visitor',
            'assigner'
        ])
        ->orderBy('assigned_at', 'desc')
        ->limit(10)
        ->get();
        
        return view('security.badges', compact(
            'badges',
            'stats',
            'assignedBadges',
            'recentActivities',
            'search',
            'status'
        ));
    }
    
    /**
     * Mark badge as lost/damaged
     */
    public function markIssue(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'issue_type' => ['required', 'in:lost,damaged'],
            'description' => ['required', 'string', 'max:500'],
            'reporter_name' => ['required', 'string', 'max:255'],
        ]);
        
        // Check if badge is currently assigned
        if ($badge->status === 'in_use') {
            $assignment = BadgeAssignment::where('badge_id', $badge->id)
                ->whereNull('returned_at')
                ->first();
                
            if ($assignment) {
                return redirect()->back()
                    ->with('error', 'Badge sedang digunakan. Harap lakukan check-out terlebih dahulu.');
            }
        }
        
        // Update badge status and create log
        $oldStatus = $badge->status;
        $badge->update([
            'status' => 'in_use', // Mark as in_use to prevent further assignment
        ]);
        
        // Create a visit log for the issue
        DB::table('visit_logs')->insert([
            'visit_request_id' => null, // No specific visit
            'action' => 'badge_issue',
            'performed_by' => Auth::id(),
            'timestamp' => now(),
            'notes' => "Badge {$badge->badge_code} dilaporkan {$validated['issue_type']} oleh {$validated['reporter_name']}: {$validated['description']}",
        ]);
        
        return redirect()->back()
            ->with('success', "Badge {$badge->badge_code} berhasil ditandai sebagai {$validated['issue_type']}.");
    }
    
    /**
     * Mark badge as recovered/fixed
     */
    public function markResolved(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'resolution' => ['required', 'string', 'max:500'],
        ]);
        
        // Check if badge is actually marked with issue
        $hasIssue = DB::table('visit_logs')
            ->whereNull('visit_request_id')
            ->where('action', 'badge_issue')
            ->where('notes', 'like', "%Badge {$badge->badge_code}%")
            ->exists();
            
        if (!$hasIssue) {
            return redirect()->back()
                ->with('error', 'Badge tidak memiliki laporan issue aktif.');
        }
        
        // Mark badge as available
        $badge->update([
            'status' => 'available',
        ]);
        
        // Create resolution log
        DB::table('visit_logs')->insert([
            'visit_request_id' => null,
            'action' => 'badge_resolved',
            'performed_by' => Auth::id(),
            'timestamp' => now(),
            'notes' => "Badge {$badge->badge_code} telah diperbaiki/ditemukan: {$validated['resolution']}",
        ]);
        
        return redirect()->back()
            ->with('success', "Badge {$badge->badge_code} berhasil ditandai sebagai tersedia kembali.");
    }
    
    /**
     * Get badge usage statistics
     */
    public function getStatistics()
    {
        // Weekly usage statistics
        $weeklyStats = DB::table('badge_assignments')
            ->selectRaw('DATE(assigned_at) as date, COUNT(*) as count')
            ->whereDate('assigned_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(assigned_at)'))
            ->orderBy('date')
            ->get();
            
        // Most used badges
        $mostUsed = DB::table('badge_assignments')
            ->join('badges', 'badge_assignments.badge_id', '=', 'badges.id')
            ->selectRaw('badges.badge_code, badges.access_area, COUNT(*) as usage_count')
            ->groupBy('badges.id', 'badges.badge_code', 'badges.access_area')
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->get();
            
        // Average usage duration
        $avgDuration = DB::table('badge_assignments')
            ->whereNotNull('returned_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, assigned_at, returned_at)) as avg_minutes')
            ->first();
            
        return response()->json([
            'weekly_stats' => $weeklyStats,
            'most_used' => $mostUsed,
            'avg_duration_minutes' => $avgDuration->avg_minutes ?? 0,
        ]);
    }
}