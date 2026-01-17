<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\VisitLog;
use App\Models\VisitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        // Get active visits (checked_in) dengan badge assignment
        $activeVisits = VisitRequest::with(['visitor', 'badgeAssignment.badge'])
            ->where('status', 'checked_in')
            ->whereHas('badgeAssignment') // Hanya yang punya badge assignment
            ->orderBy('created_at', 'desc')
            ->get();
            
        // If search query exists, search for specific active visit
        $searchResults = collect();
        if ($search) {
            $searchResults = VisitRequest::with(['visitor', 'badgeAssignment.badge'])
                ->where('status', 'checked_in')
                ->whereHas('badgeAssignment')
                ->where(function($query) use ($search) {
                    $query->whereHas('visitor', function($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('identity_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('badgeAssignment.badge', function($q) use ($search) {
                        $q->where('badge_code', 'like', "%{$search}%");
                    });
                })
                ->get();
        }
        
        return view('security.checkout', compact(
            'activeVisits',
            'search',
            'searchResults'
        ));
    }
    
    public function process(Request $request, VisitRequest $visitRequest)
    {
        // Validate that visit can be checked out
        if ($visitRequest->status !== 'checked_in') {
            return redirect()->route('security.checkout')
                ->with('error', 'Visitor belum check-in atau sudah check-out.');
        }
        
        if (!$visitRequest->badgeAssignment) {
            return redirect()->route('security.checkout')
                ->with('error', 'Data badge assignment tidak ditemukan.');
        }
        
        $validated = $request->validate([
            'condition_check' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update badge status to available
            $badge = $visitRequest->badgeAssignment->badge;
            $badge->update(['status' => 'available']);
            
            // Update badge assignment with return time
            $visitRequest->badgeAssignment->update([
                'returned_at' => now(),
            ]);
            
            // Update visit request status
            $visitRequest->update(['status' => 'checked_out']);
            
            // Create visit log
            VisitLog::create([
                'visit_request_id' => $visitRequest->id,
                'action' => 'check_out',
                'performed_by' => Auth::id(),
                'timestamp' => now(),
                'notes' => ($validated['condition_check'] ?? '') . ' | ' . ($validated['notes'] ?? ''),
            ]);
            
            DB::commit();
            
            return redirect()->route('security.checkout')
                ->with('success', "Visitor {$visitRequest->visitor->full_name} berhasil check-out. Badge {$badge->badge_code} telah dikembalikan.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('security.checkout')
                ->with('error', 'Terjadi kesalahan saat memproses check-out.');
        }
    }
}