<?php
// app/Http/Controllers/Api/SecurityApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitRequest;
use Illuminate\Http\Request;

class SecurityApiController extends Controller
{
    public function searchVisits(Request $request)
    {
        $query = $request->get('q');
        
        return VisitRequest::with(['visitor', 'division'])
            ->where('status', 'registered')
            ->where('visit_date', today())
            ->where(function($q) use ($query) {
                $q->whereHas('visitor', function($q2) use ($query) {
                    $q2->where('full_name', 'like', "%{$query}%")
                       ->orWhere('identity_number', 'like', "%{$query}%")
                       ->orWhere('institution', 'like', "%{$query}%");
                })
                ->orWhere('purpose', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($visit) {
                return [
                    'id' => $visit->id,
                    'visitor' => [
                        'full_name' => $visit->visitor->full_name,
                        'identity_number' => $visit->visitor->identity_number,
                        'institution' => $visit->visitor->institution,
                    ],
                    'purpose' => $visit->purpose,
                    'division' => [
                        'division_name' => $visit->division->division_name,
                    ],
                    'visit_date' => $visit->visit_date->format('d/m/Y'),
                    'start_time' => $visit->start_time,
                ];
            });
    }
    
    public function getTodayQueue()
    {
        return VisitRequest::with('visitor')
            ->where('status', 'registered')
            ->where('visit_date', today())
            ->orderBy('start_time')
            ->get()
            ->map(function($visit) {
                return [
                    'id' => $visit->id,
                    'visitor_name' => $visit->visitor->full_name,
                    'institution' => $visit->visitor->institution,
                    'start_time' => $visit->start_time,
                    'purpose' => $visit->purpose,
                ];
            });
    }
}