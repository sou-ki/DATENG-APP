<?php
// untuk bagian keamanan
namespace App\Http\Controllers;

use App\Models\VisitRequest;
use App\Models\Badge;
use App\Models\BadgeAssignment;
use App\Models\VisitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    public function checkIn(Request $request, $visitId)
    {
        $request->validate([
            'badge_id' => 'required|exists:badges,id'
        ]);

        $visit = VisitRequest::findOrFail($visitId);

        if ($visit->status !== 'registered') {
            abort(400, 'Visit cannot be checked in');
        }

        $badge = Badge::where('id', $request->badge_id)
                      ->where('status', 'available')
                      ->firstOrFail();

        // assign badge
        BadgeAssignment::create([
            'visit_request_id' => $visit->id,
            'badge_id'         => $badge->id,
            'assigned_by'      => Auth::id(),
            'assigned_at'      => now(),
        ]);

        $badge->update(['status' => 'in_use']);
        $visit->update(['status' => 'checked_in']);

        VisitLog::create([
            'visit_request_id' => $visit->id,
            'action'           => 'check_in',
            'performed_by'     => Auth::id(),
            'timestamp'        => now(),
        ]);

        return response()->json(['message' => 'Visitor checked in']);
    }
}
