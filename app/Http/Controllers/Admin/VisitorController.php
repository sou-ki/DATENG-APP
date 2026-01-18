<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\VisitRequest;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $query = Visitor::withCount(['visitRequests']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('identity_number', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        $visitors = $query->orderBy('full_name')->paginate(30);
        
        $stats = [
            'total' => Visitor::count(),
            'with_visits' => Visitor::has('visitRequests')->count(),
            'visits_today' => VisitRequest::whereDate('visit_date', today())->count(),
        ];
        
        return view('admin.visitors.index', compact('visitors', 'stats', 'search'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Visitor $visitor)
    {
        $visitor->loadCount('visitRequests');
        
        $visitStats = $visitor->visitRequests()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
            
        $recentVisits = $visitor->visitRequests()
            ->with(['division', 'creator', 'badgeAssignment.badge'])
            ->orderBy('visit_date', 'desc')
            ->paginate(20);
            
        return view('admin.visitors.show', compact('visitor', 'visitStats', 'recentVisits'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visitor $visitor)
    {
        // Check if visitor has visit requests
        if ($visitor->visitRequests()->exists()) {
            return redirect()->route('admin.visitors.index')
                ->with('error', 'Tidak dapat menghapus visitor yang memiliki riwayat kunjungan.');
        }
        
        $visitor->delete();
        
        return redirect()->route('admin.visitors.index')
            ->with('success', 'Visitor berhasil dihapus.');
    }
    
    /**
     * Get visitor statistics
     */
    public function statistics()
    {
        $stats = [
            'total_visitors' => Visitor::count(),
            'total_visits' => VisitRequest::count(),
            'visits_by_month' => $this->getVisitsByMonth(),
            'top_visitors' => $this->getTopVisitors(),
            'institution_stats' => $this->getInstitutionStats(),
        ];
        
        return view('admin.visitors.statistics', compact('stats'));
    }
    
    private function getVisitsByMonth()
    {
        return VisitRequest::selectRaw('YEAR(visit_date) as year, MONTH(visit_date) as month, COUNT(*) as count')
            ->whereYear('visit_date', '>=', now()->subYear()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'month' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'count' => $item->count,
                ];
            });
    }
    
    private function getTopVisitors($limit = 10)
    {
        return Visitor::withCount('visitRequests')
            ->orderBy('visit_requests_count', 'desc')
            ->limit($limit)
            ->get();
    }
    
    private function getInstitutionStats($limit = 10)
    {
        return Visitor::selectRaw('institution, COUNT(*) as visitor_count, SUM((SELECT COUNT(*) FROM visit_requests WHERE visitor_id = visitors.id)) as visit_count')
            ->groupBy('institution')
            ->orderBy('visit_count', 'desc')
            ->limit($limit)
            ->get();
    }
}