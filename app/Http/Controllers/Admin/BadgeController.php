<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\BadgeIssue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BadgeController extends Controller
{
    /**
     * Display a listing of badges.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        
        $query = Badge::withCount(['badgeAssignments'])
            ->with(['badgeAssignments' => function($query) {
                $query->latest()->limit(1);
            }]);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('badge_code', 'like', "%{$search}%")
                  ->orWhere('access_area', 'like', "%{$search}%");
            });
        }
        
        if ($status && in_array($status, ['available', 'in_use', 'maintenance'])) {
            $query->where('status', $status);
        }
        
        $badges = $query->orderBy('badge_code')->paginate(20);
        
        // Statistics
        $stats = [
            'total' => Badge::count(),
            'available' => Badge::where('status', 'available')->count(),
            'in_use' => Badge::where('status', 'in_use')->count(),
            'maintenance' => Badge::where('status', 'maintenance')->count(),
            'lost_damaged' => BadgeIssue::whereIn('status', ['reported', 'investigating'])->count(),
        ];
        
        return view('admin.badges.index', compact('badges', 'search', 'status', 'stats'));
    }

    /**
     * Show the form for creating a new badge.
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Store a newly created badge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'badge_code' => ['required', 'string', 'max:50', 'unique:badges'],
            'access_area' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['available', 'maintenance'])],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        $badge = Badge::create($validated);
        
        return redirect()->route('admin.badges.index')
            ->with('success', "Badge {$badge->badge_code} berhasil ditambahkan.");
    }

    /**
     * Display badge details.
     */
    public function show(Badge $badge)
    {
        $badge->load(['badgeAssignments' => function($query) {
            $query->with(['visitRequest.visitor', 'assigner'])
                  ->orderBy('assigned_at', 'desc');
        }]);
        
        $issues = BadgeIssue::where('badge_id', $badge->id)
            ->orderBy('reported_at', 'desc')
            ->get();
            
        $currentAssignment = $badge->badgeAssignments()
            ->whereNull('returned_at')
            ->first();
            
        // Statistics
        $stats = [
            'total_assignments' => $badge->badgeAssignments()->count(),
            'total_hours' => $this->calculateTotalUsageHours($badge),
            'avg_duration' => $this->calculateAverageDuration($badge),
            'issue_count' => $issues->count(),
        ];
        
        return view('admin.badges.show', compact('badge', 'issues', 'currentAssignment', 'stats'));
    }

    /**
     * Show the form for editing a badge.
     */
    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Update the specified badge.
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'badge_code' => ['required', 'string', 'max:50', 
                Rule::unique('badges')->ignore($badge->id)],
            'access_area' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['available', 'in_use', 'maintenance'])],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        
        $badge->update($validated);
        
        return redirect()->route('admin.badges.show', $badge)
            ->with('success', "Badge {$badge->badge_code} berhasil diperbarui.");
    }

    /**
     * Remove the specified badge.
     */
    public function destroy(Badge $badge)
    {
        // Check if badge is currently in use
        if ($badge->status === 'in_use') {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus badge yang sedang digunakan.');
        }
        
        // Check if badge has assignment history
        if ($badge->badgeAssignments()->exists()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus badge yang memiliki riwayat penggunaan.');
        }
        
        $badge->delete();
        
        return redirect()->route('admin.badges.index')
            ->with('success', "Badge {$badge->badge_code} berhasil dihapus.");
    }

    /**
     * Manage badge issues.
     */
    public function issues(Request $request)
    {
        $status = $request->query('status');
        
        $query = BadgeIssue::with(['badge', 'reporter']);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $issues = $query->orderBy('reported_at', 'desc')->paginate(20);
        
        $stats = [
            'total' => BadgeIssue::count(),
            'reported' => BadgeIssue::where('status', 'reported')->count(),
            'investigating' => BadgeIssue::where('status', 'investigating')->count(),
            'resolved' => BadgeIssue::where('status', 'resolved')->count(),
            'lost' => BadgeIssue::where('issue_type', 'lost')->count(),
            'damaged' => BadgeIssue::where('issue_type', 'damaged')->count(),
        ];
        
        return view('admin.badges.issues', compact('issues', 'status', 'stats'));
    }

    /**
     * Update issue status.
     */
    public function updateIssue(Request $request, BadgeIssue $issue)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['investigating', 'resolved', 'closed'])],
            'resolution' => ['nullable', 'string', 'max:500'],
            'resolved_by' => ['nullable', 'string', 'max:255'],
            'resolved_at' => ['nullable', 'date'],
        ]);
        
        $issue->update($validated);
        
        // If resolved and badge was in maintenance, change to available
        if ($validated['status'] === 'resolved' && $issue->badge->status === 'maintenance') {
            $issue->badge->update(['status' => 'available']);
        }
        
        return redirect()->back()
            ->with('success', "Status laporan berhasil diperbarui.");
    }

    private function calculateTotalUsageHours(Badge $badge)
    {
        $totalMinutes = $badge->badgeAssignments()
            ->whereNotNull('returned_at')
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, assigned_at, returned_at)) as total')
            ->first()->total ?? 0;
            
        return floor($totalMinutes / 60) . 'h ' . ($totalMinutes % 60) . 'm';
    }
    
    private function calculateAverageDuration(Badge $badge)
    {
        $avgMinutes = $badge->badgeAssignments()
            ->whereNotNull('returned_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, assigned_at, returned_at)) as avg')
            ->first()->avg ?? 0;
            
        return round($avgMinutes) . 'm';
    }
}