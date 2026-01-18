<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\BadgeAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        
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
        
        $stats = [
            'total' => Badge::count(),
            'available' => Badge::where('status', 'available')->count(),
            'in_use' => Badge::where('status', 'in_use')->count(),
        ];
        
        return view('admin.badges.index', compact('badges', 'stats', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'badge_code' => ['required', 'string', 'max:50', 'unique:badges'],
            'access_area' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:available,in_use'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        
        Badge::create($validated);
        
        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Badge $badge)
    {
        $badge->load(['badgeAssignments' => function($query) {
            $query->with(['visitRequest.visitor', 'assigner'])
                  ->orderBy('assigned_at', 'desc')
                  ->limit(20);
        }]);
        
        $usageStats = [
            'total_assignments' => $badge->badgeAssignments()->count(),
            'current_assignment' => $badge->badgeAssignments()->whereNull('returned_at')->first(),
            'avg_duration' => $this->getAverageDuration($badge),
        ];
        
        return view('admin.badges.show', compact('badge', 'usageStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'badge_code' => ['required', 'string', 'max:50', 
                Rule::unique('badges')->ignore($badge->id)],
            'access_area' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:available,in_use'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        
        $badge->update($validated);
        
        return redirect()->route('admin.badges.show', $badge)
            ->with('success', 'Data badge berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        // Check if badge has assignments
        if ($badge->badgeAssignments()->exists()) {
            return redirect()->route('admin.badges.index')
                ->with('error', 'Tidak dapat menghapus badge yang memiliki riwayat penggunaan.');
        }
        
        $badge->delete();
        
        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge berhasil dihapus.');
    }

    /**
     * Get usage history for a badge
     */
    public function usageHistory(Badge $badge)
    {
        $assignments = BadgeAssignment::with(['visitRequest.visitor', 'assigner'])
            ->where('badge_id', $badge->id)
            ->orderBy('assigned_at', 'desc')
            ->paginate(50);
            
        return view('admin.badges.usage-history', compact('badge', 'assignments'));
    }

    private function getAverageDuration($badge)
    {
        $assignments = $badge->badgeAssignments()
            ->whereNotNull('returned_at')
            ->get();
            
        if ($assignments->isEmpty()) {
            return 'N/A';
        }
        
        $totalMinutes = 0;
        foreach ($assignments as $assignment) {
            $totalMinutes += $assignment->assigned_at->diffInMinutes($assignment->returned_at);
        }
        
        $avgMinutes = $totalMinutes / $assignments->count();
        
        if ($avgMinutes < 60) {
            return round($avgMinutes) . ' menit';
        } elseif ($avgMinutes < 1440) {
            return round($avgMinutes / 60, 1) . ' jam';
        } else {
            return round($avgMinutes / 1440, 1) . ' hari';
        }
    }
}