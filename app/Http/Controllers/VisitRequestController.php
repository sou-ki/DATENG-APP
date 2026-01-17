<?php
// untuk bagian internal
namespace App\Http\Controllers;

use App\Models\VisitRequest;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitRequestController extends Controller
{
    // Internal membuat rencana kunjungan
    public function store(Request $request)
    {
        $request->validate([
            'full_name'       => 'required|string',
            'identity_number' => 'required|string',
            'institution'     => 'nullable|string',
            'phone_number'    => 'nullable|string',
            'division_id'     => 'required|exists:divisions,id',
            'purpose'         => 'required|string',
            'visit_type'      => 'required|string',
            'visit_date'      => 'required|date',
            'start_time'      => 'required',
        ]);

        // visitor bisa lama atau baru
        $visitor = Visitor::firstOrCreate(
            ['identity_number' => $request->identity_number],
            $request->only(['full_name', 'institution', 'phone_number'])
        );

        $visit = VisitRequest::create([
            'visitor_id' => $visitor->id,
            'division_id'=> $request->division_id,
            'purpose'    => $request->purpose,
            'visit_type' => $request->visit_type,
            'visit_date' => $request->visit_date,
            'start_time' => $request->start_time,
            'created_by' => Auth::id(),
            'status'     => 'registered',
        ]);

        return response()->json([
            'message' => 'Visit request created',
            'data'    => $visit
        ]);
    }
}
