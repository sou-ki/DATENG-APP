<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\VisitLog;
use App\Models\VisitRequest;
use App\Models\BadgeAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $availableBadges = Badge::where('status', 'available')->get();
        
        // Get pending visits for today
        $pendingVisits = VisitRequest::with(['visitor', 'division'])
            ->whereDate('visit_date', today())
            ->where('status', 'registered')
            ->orderBy('start_time')
            ->get();
            
        // If search query exists, search for specific visit
        $searchResults = collect();
        if ($search) {
            $searchResults = VisitRequest::with(['visitor', 'division'])
                ->whereDate('visit_date', today())
                ->where('status', 'registered')
                ->where(function($query) use ($search) {
                    $query->whereHas('visitor', function($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                          ->orWhere('identity_number', 'like', "%{$search}%");
                    });
                })
                ->get();
        }
        
        return view('security.checkin', compact(
            'availableBadges', 
            'pendingVisits',
            'search',
            'searchResults'
        ));
    }
    
    public function process(Request $request, VisitRequest $visitRequest)
    {
        // Validate that visit can be checked in
        if ($visitRequest->status !== 'registered') {
            return redirect()->route('security.checkin')
                ->with('error', 'Kunjungan ini sudah diproses atau tidak dapat di-check-in.');
        }
        
        if ($visitRequest->visit_date->toDateString() !== today()->toDateString()) {
            return redirect()->route('security.checkin')
                ->with('error', 'Hanya kunjungan hari ini yang dapat di-check-in.');
        }
        
        $validated = $request->validate([
            'badge_id' => ['required', 'exists:badges,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        
        // Check if badge is available
        $badge = Badge::find($validated['badge_id']);
        if ($badge->status !== 'available') {
            return redirect()->route('security.checkin')
                ->with('error', 'Badge tidak tersedia. Silakan pilih badge lain.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update badge status
            $badge->update(['status' => 'in_use']);
            
            // Create badge assignment
            BadgeAssignment::create([
                'visit_request_id' => $visitRequest->id,
                'badge_id' => $badge->id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'returned_at' => null,
            ]);
            
            // Update visit request status
            $visitRequest->update(['status' => 'checked_in']);
            
            // Create visit log
            VisitLog::create([
                'visit_request_id' => $visitRequest->id,
                'action' => 'check_in',
                'performed_by' => Auth::id(),
                'timestamp' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);
            
            DB::commit();
            
            return redirect()->route('security.checkin')
                ->with('success', "Visitor {$visitRequest->visitor->full_name} berhasil check-in dengan badge {$badge->badge_code}.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('security.checkin')
                ->with('error', 'Terjadi kesalahan saat memproses check-in.');
        }
    }
}