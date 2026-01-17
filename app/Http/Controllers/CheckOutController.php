<?php
// untuk bagian keamanan
namespace App\Http\Controllers;

use App\Models\VisitRequest;
use App\Models\VisitLog;
use Illuminate\Support\Facades\Auth;

class CheckOutController extends Controller
{
    public function checkOut($visitId)
    {
        $visit = VisitRequest::with('badgeAssignment.badge')
                             ->findOrFail($visitId);

        if ($visit->status !== 'checked_in') {
            abort(400, 'Visit not in checked-in state');
        }

        $assignment = $visit->badgeAssignment;
        $badge = $assignment->badge;

        $assignment->update([
            'returned_at' => now()
        ]);

        $badge->update(['status' => 'available']);
        $visit->update(['status' => 'checked_out']);

        VisitLog::create([
            'visit_request_id' => $visit->id,
            'action'           => 'check_out',
            'performed_by'     => Auth::id(),
            'timestamp'        => now(),
        ]);

        return response()->json(['message' => 'Visitor checked out']);
    }
}
